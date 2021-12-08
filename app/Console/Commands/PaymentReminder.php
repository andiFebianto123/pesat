<?php

namespace App\Console\Commands;

use App\Models\OrderDt;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $orders = DB::table('order_hd')
        ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
        ->where('odt.has_child',0)
        ->where('payment_status',1)
        ->where('odt.has_remind',0)
        ->get();

        foreach($orders as $key => $order){
            if($order->monthly_subscription !=1){
                $startdate = Carbon::parse($order->start_order_date);
                $enddate   = Carbon::parse($order->end_order_date);
                $interval  = $enddate->diffInDays($startdate);

                $intervalneworder = $enddate->addMonthsNoOverflow(-1);
                $intervalcreateorder = $startdate->diffInDays($intervalneworder);
                $intervalremind=$intervalcreateorder + 14;
                ///////////
                $now=Carbon::now();
    
                $intervalnow=$startdate->diffInDays($now);

                if($intervalnow >= $intervalremind){
                    OrderDt::where('order_dt_id',$order->order_dt_id)
                            ->where('order_id', $order->order_id)
                            ->where('child_id', $order->child_id)
                            ->where('has_child',0)
                            ->update(['has_remind' => 1]);
  
                }


            }else{
                $startdate = Carbon::parse($order->start_order_date);
                $enddate   = Carbon::parse($order->end_order_date);
                $interval  = $enddate->diffInDays($startdate);

                $intervalcreateorder = $interval-7;
                $intervalremind=$intervalcreateorder + 3;
                $now=Carbon::now();
                $intervalnow=$startdate->diffInDays($now);

                if($intervalnow >= $intervalremind ){
                    OrderDt::where('order_dt_id', $order->order_dt_id)
                            ->where('child_id', $order->child_id)
                            
                            ->update(['has_remind' => 1]);
                }

            }

        }

       // return Command::SUCCESS;
    }
}
