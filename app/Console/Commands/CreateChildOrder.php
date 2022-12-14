<?php

namespace App\Console\Commands;

use PDF;
use Exception;
use Carbon\Carbon;
use App\Models\DataOrder;
use App\Mail\ReminderOrder;
use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\Midtrans\CreateSnapTokenService;

class CreateChildOrder extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:childorder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {

            $now = Carbon::now();
            $dateafteronemont = $now->copy()->addMonthsNoOverflow(1);
            $orders = DataDetailOrder::join('order_hd', 'order_hd.order_id', '=', 'order_dt.order_id')
                ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
                ->join('child_master as cm', 'cm.child_id', '=', 'order_dt.child_id')
                ->where('order_dt.has_child', 0)
                ->where('order_dt.monthly_subscription', '!=', 1)
                ->where('order_dt.end_order_date', '<=', $dateafteronemont)
                ->where('payment_status', 2)
                ->where('order_hd.deleted_at', null)
                ->addSelect(
                    'order_dt.has_child',
                    'order_hd.order_id', 'order_hd.parent_order_id', 'order_hd.order_no', 'order_hd.total_price', 'order_hd.payment_status',
                    'order_dt.order_dt_id', 'order_dt.parent_order_dt_id', 'order_dt.price', 'order_dt.monthly_subscription', 'order_dt.start_order_date', 'order_dt.end_order_date',
                    'sm.sponsor_id', 'sm.full_name as sponsor_name', 'sm.email', 'sm.address as sponsor_address', 'sm.no_hp', 'cm.child_id', 'cm.full_name as child_name',
                    'cm.registration_number', 'cm.gender', 'cm.date_of_birth', 'cm.class', 'cm.school', 'cm.school_year'
                )
                ->get();
            $this->handleCreateChildOrder($orders);

            $dateafteroneweek = $now->copy()->addDay(7);
            $orders = DataDetailOrder::join('order_hd', 'order_hd.order_id', '=', 'order_dt.order_id')
                ->join('sponsor_master as sm', 'sm.sponsor_id', '=', 'order_hd.sponsor_id')
                ->join('child_master as cm', 'cm.child_id', '=', 'order_dt.child_id')
                ->where('order_dt.has_child', 0)
                ->where('order_dt.monthly_subscription', '=', 1)
                ->where('order_dt.end_order_date', '<=', $dateafteroneweek)
                ->where('payment_status', 2)
                ->where('order_hd.deleted_at', null)
                ->addSelect(
                    'order_dt.has_child',
                    'order_hd.order_id', 'order_hd.parent_order_id', 'order_hd.order_no', 'order_hd.total_price', 'order_hd.payment_status',
                    'order_dt.order_dt_id', 'order_dt.parent_order_dt_id', 'order_dt.price', 'order_dt.monthly_subscription', 'order_dt.start_order_date', 'order_dt.end_order_date',
                    'sm.sponsor_id', 'sm.full_name as sponsor_name', 'sm.email', 'sm.address as sponsor_address', 'sm.no_hp', 'cm.child_id', 'cm.full_name as child_name', 'cm.price as cm_price',
                    'cm.registration_number', 'cm.gender', 'cm.date_of_birth', 'cm.class', 'cm.school', 'cm.school_year'
                )
                ->get();
            $this->handleCreateChildOrder($orders);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('cron')->info('ERROR CRON JOB CreateChildOrder');
            Log::channel('cron')->error($e);
        }

    }

    private function handleCreateChildOrder($orders){
        $uniqueOrderId = [];
        foreach($orders as $order){
            $sponsorid = $order->sponsor_id;
            $totalPrice = $order->cm_price;
            $dataOrder = DataOrder::create([
                'parent_order_id' => $order->order_id,
                'sponsor_id' => $sponsorid,
                'payment_status' => 1,
                'total_price' => $totalPrice,
            ]);

            $startOrderdate = Carbon::parse($order->end_order_date);

            $detailOrder = new DataDetailOrder();
            $detailOrder->parent_order_dt_id = $order->order_dt_id;
            $detailOrder->order_id = $dataOrder->order_id;
            $detailOrder->child_id = $order->child_id;
            $detailOrder->price = $totalPrice;
            $detailOrder->monthly_subscription = $order->monthly_subscription;
            $detailOrder->start_order_date = $startOrderdate;
            $detailOrder->end_order_date = $startOrderdate->copy()->addMonthsNoOverflow($order->monthly_subscription);
            $detailOrder->save();

            $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id', $dataOrder->order_id)
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

            $midtrans = new CreateSnapTokenService($Snaptokenorder, $dataOrder->order_id);
            $snapToken = $midtrans->getSnapToken();
            $dataOrder->snap_token = $snapToken;
            $dataOrder->order_id_midtrans = 'anak-' . $dataOrder->order_id;
            $dataOrder->total_price = $totalPrice;
            $dataOrder->save();

            $data["email"] = $order->email;
            $data["title"] = "Reminder for Your Subscription";
            $data["body"] = "This is Demo";
            $data["sponsor_name"] = $order->sponsor_name;
            $data["order_id"] = $dataOrder->order_id;
            $data["sponsor_address"] = $order->sponsor_address;
            $data["no_hp"] = $order->no_hp;
            $data["child_name"] = $order->child_name;
            $data["monthly_subscription"] = $order->monthly_subscription;
            $data["price"] = $totalPrice;
            $data["total_price"] = $totalPrice;
            $data["date_now"] = $startOrderdate->format('Y-m-d');

            $order->has_child = 1;
            $order->save();
            $uniqueOrderId[$dataOrder->order_id] = true;
        }

        foreach($uniqueOrderId as $orderId => $boolean){
            $orderDetails = DataDetailOrder::where('order_id', $orderId)->with('childname')->get();
            $order = DataOrder::where('order_id', $orderId)->with('sponsorname')->first();
            if($order != null){
                Mail::to($order->sponsorname->email)
                ->send(new ReminderOrder($order, $orderDetails, 'Info Pembaharuan Donasi di Pesat #' . $order->order_id, 'Info Pembaharuan Donasi di Pesat #' . $order->order_id, false));
            }
        }
    }

}
