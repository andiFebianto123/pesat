<?php

namespace App\Console\Commands;

use PDF;
use Exception;
use Carbon\Carbon;
use App\Models\DataOrder;
use App\Models\DataDetailOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:reminder';

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
            $dateafter2weeks = $now->copy()->addDay(14);
            $orders = DataDetailOrder::join('order_hd', 'order_hd.order_id', '=', 'order_dt.order_id')
                ->join('sponsor_master as sm', 'order_hd.sponsor_id', '=', 'sm.sponsor_id')
                ->join('child_master as cm', 'cm.child_id', '=', 'order_dt.child_id')
                ->where('order_dt.has_child', 0)
                ->where('order_dt.start_order_date', '<=', $dateafter2weeks)
                ->where('order_dt.has_remind', 0)
                ->where('payment_status', 1)
                ->where('order_dt.monthly_subscription', '!=', 1)
                ->where('order_hd.deleted_at', null)
                ->whereNotNull('order_hd.parent_order_id')
                ->addSelect(
                    'order_dt.has_remind',
                    'order_hd.order_id', 'order_hd.parent_order_id', 'order_hd.order_no', 'order_hd.total_price', 'order_hd.payment_status',
                    'order_hd.payment_status', 'order_dt.order_dt_id', 'order_dt.parent_order_dt_id', 'order_dt.price', 'order_dt.monthly_subscription', 'order_dt.start_order_date',
                    'order_dt.end_order_date', 'sm.sponsor_id', 'sm.full_name as sponsor_name', 'sm.email', 'sm.address as sponsor_address', 'sm.no_hp',
                    'cm.child_id', 'cm.full_name as child_name', 'cm.registration_number', 'cm.gender', 'cm.date_of_birth', 'cm.class', 'cm.school', 'cm.school_year'
                )
                ->get();
            
            $this->handlePaymentReminder($orders);

            $dateafter3days = $now->copy()->addDay(3);
            $orders1month = DataDetailOrder::join('order_hd', 'order_hd.order_id', '=', 'order_dt.order_id')
                ->join('sponsor_master as sm', 'order_hd.sponsor_id', '=', 'sm.sponsor_id')
                ->join('child_master as cm', 'cm.child_id', '=', 'order_dt.child_id')
                ->where('order_dt.has_child', 0)
                ->where('order_dt.start_order_date', '<=', $dateafter3days)
                ->where('order_dt.monthly_subscription', 1)
                ->where('order_dt.has_remind', 0)
                ->where('payment_status', 1)
                ->where('order_hd.deleted_at', null)
                ->whereNotNull('order_hd.parent_order_id')
                ->addSelect(
                    'order_dt.has_remind',
                    'order_hd.order_id', 'order_hd.parent_order_id', 'order_hd.order_no', 'order_hd.total_price', 'order_hd.payment_status',
                    'order_dt.parent_order_dt_id', 'order_dt.price', 'order_dt.order_dt_id', 'order_dt.monthly_subscription', 'order_dt.start_order_date', 'order_dt.end_order_date',
                    'sm.sponsor_id', 'sm.full_name as sponsor_name', 'sm.email', 'sm.address as sponsor_address', 'sm.no_hp', 'cm.child_id',
                    'cm.full_name as child_name', 'cm.registration_number', 'cm.gender', 'cm.date_of_birth', 'cm.class', 'cm.school', 'cm.school_year'
                )
                ->get();
            $this->handlePaymentReminder($orders1month);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('cron')->info('ERROR CRON JOB PaymentReminder');
            Log::channel('cron')->error($e);
        }
    }

    public function handlePaymentReminder($orders){
        foreach ($orders as $key => $order) {
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
            $data["no_hp"] = $order->no_hp;
            $data["date_now"] = Carbon::parse($order->start_order_date)->format('Y-m-d');

            $pdf = PDF::loadView('Email.PaymentReminder', $data);

            Mail::send('Email.BodyPaymentReminder', $data, function ($message) use ($data, $pdf) {
                $message->to($data["email"], $data["email"])
                    ->subject($data["title"])
                    ->attachData($pdf->output(), $data['order_id'] . "_" . $data['sponsor_name'] . ".pdf");
            });

            $order->has_remind = 1;
            $order->save();
        }
    }
}
