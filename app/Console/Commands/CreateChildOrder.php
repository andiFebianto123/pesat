<?php

namespace App\Console\Commands;

use App\Models\OrderDt;
use App\Models\OrderHd;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $orders = DB::table('order_hd')
        ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
        ->where('odt.has_child',0)
        ->get();


        foreach($orders as $key => $order){

            if($order->monthly_subscription !=1){
                
                $startdate = Carbon::parse($order->start_order_date);
                $enddate   = Carbon::parse($order->end_order_date);
                $interval  = $enddate->diffInDays($startdate);

                $intervalneworder = $enddate->addMonthsNoOverflow(-1);
                $intervalcreateorder = $startdate->diffInDays($intervalneworder);
                ///////////
                $now=Carbon::now();
    
                $intervalnow=$startdate->diffInDays($now);

                if($intervalnow >=$intervalcreateorder){
                    $insertorderhd = new OrderHd();
                    $insertorderhd->parent_order_id = $order->order_id;
                    $insertorderhd->order_no        = $order->order_no;
                    $insertorderhd->sponsor_id      = $order->sponsor_id;
                    $insertorderhd->total_price     = $order->total_price;
                    $insertorderhd->payment_status  = 1;
                    $insertorderhd->save();
        
        
                    //update has_child
                    OrderDt::where('order_id', $order->order_id)
                            ->where('child_id', $order->child_id)
                            ->update(['has_child' => 1]);
        
                    $newstartdate=Carbon::parse($order->end_order_date);
                    $insertorderdt = new OrderDt();
                    $insertorderdt->child_id             = $order->child_id;
                    $insertorderdt->order_id             = $order->order_id;
                    $insertorderdt->price                = $order->price;
                    $insertorderdt->monthly_subscription = $order->monthly_subscription;
                    $insertorderdt->start_order_date     = $newstartdate;
                    $insertorderdt->end_order_date       = $newstartdate->copy()->addMonthsNoOverflow($order->monthly_subscription);
                    
                    $insertorderdt->save();
                }
    
                



            }else{
                $startdate = Carbon::parse($order->start_order_date);
                $enddate   = Carbon::parse($order->end_order_date);
                $interval  = $enddate->diffInDays($startdate);

                $intervalcreateorder = $interval-7;
                $now=Carbon::now();
                $intervalnow=$startdate->diffInDays($now);
                
                if($intervalnow >= $intervalcreateorder){
                    $insertorderhd = new OrderHd();
                    $insertorderhd->parent_order_id = $order->order_id;
                    $insertorderhd->order_no        = $order->order_no;
                    $insertorderhd->sponsor_id      = $order->sponsor_id;
                    $insertorderhd->total_price     = $order->total_price;
                    $insertorderhd->payment_status  = 1;
                    $insertorderhd->save();
        
        
                    //update has_child
                    OrderDt::where('order_id', $order->order_id)
                            ->where('child_id', $order->child_id)
                            ->update(['has_child' => 1]);
        
                    $newstartdate=Carbon::parse($order->end_order_date);
                    $insertorderdt = new OrderDt();
                    $insertorderdt->child_id             = $order->child_id;
                    $insertorderdt->order_id             = $order->order_id;
                    $insertorderdt->price                = $order->price;
                    $insertorderdt->monthly_subscription = $order->monthly_subscription;
                    $insertorderdt->start_order_date     = $newstartdate;
                    $insertorderdt->end_order_date       = $newstartdate->copy()->addMonthsNoOverflow($order->monthly_subscription);
                    
                    $insertorderdt->save();

                }


                



            }
           
        }


    }

}