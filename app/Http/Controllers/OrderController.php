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
use Illuminate\Support\Facades\Validator;
use App\Services\Midtrans\CreateSnapTokenService;
use App\Services\Midtrans\UpdateSnapTokenServiceForExpiredTransaction;

class OrderController extends Controller
{
    public function index($id, Request $request){
        $child = ChildMaster::where('child_id', $id)->first();
        $now = Carbon::now()->startOfDay();

        if($child == null){
            return redirect(url('list-child'))->with(['error' => 'Order anak yang dimaksud tidak ditemukan.']);
        }

        if ($child->is_sponsored || ChildMaster::getStatusSponsor($child->child_id, $now)) {
            return redirect(url('childdetail/' . $id))->with(['errorsponsor' => 'Maaf, Anda sudah tidak dapat melakukan sponsor karena anak telah memiliki sponsor lain.']);
        }

        $validator = Validator::make($request->all(), ['monthly_subscription' => ['required', Rule::in([1,3,6,12])]]);

        if($validator->fails()){
            return redirect(url('childdetail/' . $id))->withInput()->withErrors($validator->errors());
        }

        $data['child'] = $child;
        $data['title'] = 'Donasi Anak';
        $data['period'] = $request->monthly_subscription;
        $data['total'] = $request->monthly_subscription * $child->price;
        return view('childorder', $data);
    }


    public function postOrder($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $childmaster = ChildMaster::where('child_id', $id)->first();
            $now = Carbon::now()->startOfDay();

            if($childmaster == null){
                DB::rollback();
                return redirect(url('list-child'))->with(['error' => 'Order anak yang dimaksud tidak ditemukan.']);
            }

            if ($childmaster->is_sponsored || ChildMaster::getStatusSponsor($childmaster->child_id, $now)) {
                DB::rollback();
                return redirect(url('childdetail/' . $id))->with(['errorsponsor' => 'Maaf, Anda sudah tidak dapat melakukan sponsor karena anak telah memiliki sponsor lain.']);
            }

            $validator = Validator::make($request->all(), ['monthly_subscription' => ['required', Rule::in([1,3,6,12])]]);

            if($validator->fails()){
                DB::rollback();
                return redirect(url('childdetail/' . $id))->withInput()->withErrors($validator->errors());
            }

            $totalprice = $request->monthly_subscription * $childmaster->price;

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
            $orders->child_id = $id;
            $orders->price = $totalprice;
            $orders->monthly_subscription = $request->monthly_subscription;
            $orders->start_order_date = $startOrderdate;
            $orders->end_order_date = $startOrderdate->copy()->addMonthsNoOverflow($request->monthly_subs);
            $orders->save();

            $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id', $OrderId)
                ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
                ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
                ->whereNull('odt.deleted_at')
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

            $orderDetails = DataDetailOrder::where('order_id', $id)->with('childname')->get();
            $createdAt = Carbon::parse($orderDetails->first()->start_order_date)->startOfDay();

            if ($transaction == 'expire' && $nowSub2Days <= $createdAt) {

                $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id', $id)
                    ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
                    ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
                    ->whereNull('odt.deleted_at')
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
            $data['orderDetails'] = $orderDetails;
            $data['title'] = 'Checkout Donasi Anak #' . $order->order_id;

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
    
}
