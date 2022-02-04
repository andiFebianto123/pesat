<?php

namespace App\Http\Controllers;

use PDF;
use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\DataOrder;
use App\Models\ChildMaster;
use App\Models\OrderProject;
use Illuminate\Http\Request;
use App\Models\ProjectMaster;
use App\Models\DataDetailOrder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use App\Services\Midtrans\CreateSnapTokenService;
use App\Services\Midtrans\UpdateSnapTokenServiceForExpiredTransaction;

class OrderController extends Controller
{
 
    public function index($id, Request $request)
    {
        $request->validate(['monthly_subscription' => ['required', Rule::in([1,3,6,12])]]);

        DB::beginTransaction();

        try {
            $user = auth()->user();
            $childmaster = ChildMaster::where('child_id', $id)->first();
            $totalprice = $request->monthly_subscription * $childmaster->price;
            $now = Carbon::now()->startOfDay();

            if($childmaster == null){
                DB::rollback();
                return redirect(url('list-child'))->with(['error' => 'Order anak yang dimaksud tidak ditemukan.']);
            }

            if (ChildMaster::getStatusSponsor($childmaster->child_id, $now)) {
                DB::rollback();
                return redirect(url('childdetail/' . $id))->with(['errorsponsor' => 'Maaf, Anda sudah tidak dapat melakukan sponsor karena anak telah memiliki sponsor lain.']);
            }

            $idsponsor = $user->sponsor_id;

            // save table order_hd
            $order = DataOrder::create(
                [
                    'parent_order_id' => null,
                    'sponsor_id' => $idsponsor,
                    'total_price' => $totalprice,
                    'payment_status' => 1,
                ]
            );

            $OrderId = $order->order_id;
            // save table order_dt
            $startOrderdate = Carbon::now();
            $orders = new DataDetailOrder();
            $orders->order_id = $OrderId;
            $orders->child_id = $request->childid;
            $orders->price = $totalprice;
            $orders->monthly_subscription = $request->monthly_subscription;
            $orders->start_order_date = $startOrderdate;
            $orders->end_order_date = $startOrderdate->copy()->addMonthsNoOverflow($request->monthly_subs);
            $orders->save();

            $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id', $OrderId)
                ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
                ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
                ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
                ->select(
                    'order_hd.*',
                    'odt.*',
                    'cm.full_name',
                    'sm.full_name as sponsor_name',
                    'sm.email',
                    'sm.no_hp'
                )
                ->get();

            $getTotalPrice = DataDetailOrder::groupBy('order_id')
                ->where('order_id', $OrderId)
                ->sum('price');

            $midtrans = new CreateSnapTokenService($Snaptokenorder, $OrderId);
            $snapToken = $midtrans->getSnapToken();

            $order->snap_token = $snapToken;
            $order->order_id_midtrans = 'anak-' . $order->order_id;
            $order->total_price = $getTotalPrice;
            $order->save();

            DB::commit();

            return Redirect::route('ordercheckout', array('id' => $OrderId));
        } catch (Execption $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function orderdonation($id)
    {
        DB::beginTransaction();
        try {
            $order = DataOrder::where('order_id', $id)->first();
            if ($order == null) {
                DB::rollBack();
                return redirect(url('child-donation'))->with(['error' => 'Order anak yang dimaksud tidak ditemukan.']);
            }
            $data['error'] = '';

            $getStatusMidtrans = $order->order_id_midtrans;
            $transaction = null;

            try {
                $decoderespon = \Midtrans\Transaction::status($getStatusMidtrans);
                $transaction = $decoderespon->transaction_status;
            } catch (Exception $e) {
                if ($e->getCode() != 404) {
                    $data['error'] = "Gagal mendapatkan status order dari Midtrans. [" . $e->getCode() . "]";
                    $data['error_status'] = $e->getCode();
                }
            }

            $now = Carbon::now()->startOfDay();
            $nowSub2Days = $now->copy()->addDay(-2);

            $createdAt = Carbon::parse(DataDetailOrder::where('order_id', $id)->first()->start_order_date)->startOfDay();

            if ($transaction == 'expire' && $nowSub2Days <= $createdAt) {

                $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id', $id)
                    ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
                    ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
                    ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
                    ->select(
                        'order_hd.*',
                        'odt.*',
                        'cm.full_name',
                        'sm.full_name as sponsor_name',
                        'sm.email',
                        'sm.no_hp'
                    )
                    ->get();

                $code = $order->order_id . "-" . Carbon::now()->timestamp;
                $orderIdMidtrans = "anak-" . $code;
                $midtrans = new CreateSnapTokenService($Snaptokenorder, $code);
                $snapToken = $midtrans->getSnapToken();
                $order->snap_token = $snapToken;
                $order->order_id_midtrans = $orderIdMidtrans;
                $order->save();
            }
            DB::commit();

            $data['order'] = $order;
            $data['snapToken'] =  $order->snap_token;

            return view('showpayment', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cekstatus()
    {

        $datas = DataOrder::where('payment_status', 1)->get();

        foreach ($datas as $data) {
            $orderno = $data->order_no;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/" . $orderno . "/status",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "\n\n",
                CURLOPT_HTTPHEADER => array(
                    "Accept: application/json",
                    "Content-Type: application/json",
                    "Authorization: Basic U0ItTWlkLXNlcnZlci1oU0ZrQnBzRi02cHBKRkRmOTNxdFZLRGc6",
                ),
            ));

            $response = curl_exec($curl);
            $decoderespon = json_decode($response, true);
            curl_close($curl);

            if ($decoderespon['status_code'] == '200' && $decoderespon['transaction_status'] == 'settlement') {
                DataOrder::where('order_no', $orderno)
                    ->update(['payment_status' => 2]);
            }
            if ($decoderespon['status_code'] == '407') {
                DataOrder::where('order_no', $orderno)
                    ->update(['payment_status' => 3]);
            }
        }
    }

    public function reminderinvoice()
    {

        DB::beginTransaction();
        try {

            $now = Carbon::now();
            $dateafter2weeks = $now->copy()->addDay(14);
            $orders = DB::table('order_hd')
                ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
                ->Join('sponsor_master as sm', 'order_hd.sponsor_id', '=', 'sm.sponsor_id')
                ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
                ->where('odt.has_child', 0)
                ->where('odt.start_order_date', '<=', $dateafter2weeks)
                ->where('odt.has_remind', 0)
                ->where('payment_status', 1)
                ->where('odt.monthly_subscription', '!=', 1)
                ->where('order_hd.deleted_at', null)
                ->where('odt.deleted_at', null)
                ->whereNotNull('order_hd.parent_order_id')
                ->addSelect(
                    'order_hd.order_id',
                    'order_hd.parent_order_id',
                    'order_hd.order_no',
                    'order_hd.total_price',
                    'order_hd.payment_status',
                    'order_hd.payment_status',
                    'odt.order_dt_id',
                    'odt.parent_order_dt_id',
                    'odt.price',
                    'odt.monthly_subscription',
                    'odt.start_order_date',
                    'odt.end_order_date',
                    'sm.sponsor_id',
                    'sm.full_name as sponsor_name',
                    'sm.email',
                    'sm.address as sponsor_address',
                    'sm.no_hp',
                    'cm.child_id',
                    'cm.full_name as child_name',
                    'cm.registration_number',
                    'cm.gender',
                    'cm.date_of_birth',
                    'cm.class',
                    'cm.school',
                    'cm.school_year'
                )

                ->get();
            if ($orders) {
                foreach ($orders as $key => $order) {


                    //                    update has_child
                    DataDetailOrder::where('order_id', $order->order_id)
                        ->where('child_id', $order->child_id)
                        ->update(['has_remind' => 1]);

                    $datenow = Carbon::now();
                    $formatdatenow = date('Y-m-d', strtotime($datenow));
                    $data["email"] = $order->email;
                    $data["title"] = "Peringatan";
                    $data["body"] = "-";
                    $data["order_id"] = $order->order_id;
                    $data["child_name"] = $order->child_name;
                    $data["sponsor_name"] = $order->sponsor_name;
                    $data["total_price"] = $order->total_price;
                    $data["price"] = $order->price;
                    $data["monthly_subscription"] = $order->monthly_subscription;
                    $data["sponsor_address"] = $order->sponsor_address;
                    $data["no_hp"]      = $order->no_hp;
                    $data["date_now"]  = $formatdatenow;

                    $pdf = PDF::loadView('Email.PaymentReminder', $data);

                    Mail::send('Email.BodyPaymentReminder', $data, function ($message) use ($data, $pdf) {
                        $message->to($data["email"], $data["email"])
                            ->subject($data["title"])
                            ->attachData($pdf->output(), $data['order_id'] . "_" . $data['sponsor_name'] . ".pdf");
                    });

                    //         DB::commit();
                }
            }

            $now = Carbon::now();
            $dateafter3days = $now->copy()->addDay(3);
            $orders1month = DB::table('order_hd')
                ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
                ->Join('sponsor_master as sm', 'order_hd.sponsor_id', '=', 'sm.sponsor_id')
                ->join('child_master as cm', 'cm.child_id', '=', 'odt.child_id')
                ->where('odt.has_child', 0)
                ->where('odt.start_order_date', '<=', $dateafter3days)
                ->where('odt.monthly_subscription', 1)
                ->where('odt.has_remind', 0)
                ->where('payment_status', 1)
                ->where('order_hd.deleted_at', null)
                ->where('odt.deleted_at', null)
                ->whereNotNull('order_hd.parent_order_id')
                ->addSelect(
                    'order_hd.order_id',
                    'order_hd.parent_order_id',
                    'order_hd.order_no',
                    'order_hd.total_price',
                    'order_hd.payment_status',
                    'odt.parent_order_dt_id',
                    'odt.price',
                    'odt.order_dt_id',
                    'odt.monthly_subscription',
                    'odt.start_order_date',
                    'odt.end_order_date',
                    'sm.sponsor_id',
                    'sm.full_name as sponsor_name',
                    'sm.email',
                    'sm.address as sponsor_address',
                    'sm.no_hp',
                    'cm.child_id',
                    'cm.full_name as child_name',
                    'cm.registration_number',
                    'cm.gender',
                    'cm.date_of_birth',
                    'cm.class',
                    'cm.school',
                    'cm.school_year'
                )
                ->get();
            if ($orders1month) {

                foreach ($orders1month as $key => $order) {


                    //                    update has_child
                    DataDetailOrder::where('order_id', $order->order_id)
                        ->where('child_id', $order->child_id)
                        ->update(['has_remind' => 1]);

                    $datenow = Carbon::now();
                    $formatdatenow = date('Y-m-d', strtotime($datenow));

                    $data["email"] = $order->email;
                    $data["title"] = "Peringatan";
                    $data["body"] = "-";
                    $data["order_id"] = $order->order_id;
                    $data["no_hp"]  = $order->no_hp;
                    $data["child_name"] = $order->child_name;
                    $data["sponsor_name"] = $order->sponsor_name;
                    $data["monthly_subscription"] = $order->monthly_subscription;
                    $data["sponsor_address"] = $order->sponsor_address;
                    $data["total_price"] = $order->total_price;
                    $data["price"] = $order->price;
                    $data["date_now"]  = $formatdatenow;


                    Mail::send('Email.BodyPaymentReminder', $data, function ($message) use ($data) {
                        $message->to($data["email"], $data["email"])
                            ->subject($data["title"]);
                    });
                    DB::commit();
                }
            }
        } catch (Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }
}
