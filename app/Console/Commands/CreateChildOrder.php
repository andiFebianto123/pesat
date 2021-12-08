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
        $now=Carbon::now();
        $dateafteronemont= $now->copy()->addMonthsNoOverflow(1);
        $orders = DB::table('order_hd')
        ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
        ->where('odt.has_child',0)
        ->where('odt.end_order_date','<=',$dateafteronemont)
        ->where('payment_status',2)
        ->where('order_hd.deleted_at',null)
        ->where('odt.deleted_at',null)
        ->get();

        foreach($orders as $key =>$order){

                    $lastorderId = DB::table('order_hd')->insertGetId(
                        [ 'parent_order_id' => $order->order_id,
                          'order_no'        => $order->order_no,
                          'sponsor_id'      => $order->sponsor_id,
                          'total_price'     => $order->total_price,
                          'payment_status'  => 1
                        ]
                    );    
//                    update has_child
                    OrderDt::where('order_id', $order->order_id)
                            ->where('child_id', $order->child_id)
                            ->update(['has_child' => 1]);
        
                    $newstartdate=Carbon::parse($order->end_order_date);
                    $insertorderdt = new OrderDt();
                    $insertorderdt->parent_order_dt_id   = $order->order_dt_id;
                    $insertorderdt->child_id             = $order->child_id;
                    $insertorderdt->order_id             = $lastorderId;
                    $insertorderdt->price                = $order->price;
                    $insertorderdt->monthly_subscription = $order->monthly_subscription;
                    $insertorderdt->start_order_date     = $newstartdate;
                    $insertorderdt->end_order_date       = $newstartdate->copy()->addMonthsNoOverflow($order->monthly_subscription);
                    
                    $insertorderdt->save();

        }


        $now=Carbon::now();
        $dateafteronemont= $now->copy()->addDay(7);
        $orders1month = DB::table('order_hd')
        ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
        ->where('odt.has_child',0)
        ->where('odt.end_order_date','<=',$dateafteronemont)
        ->where('payment_status',2)
        ->where('order_hd.deleted_at',null)
        ->where('odt.deleted_at',null)
        ->get();
        foreach($orders1month as $key =>$order){

            $lastorderId = DB::table('order_hd')->insertGetId(
                [ 'parent_order_id' => $order->order_id,
                  'order_no'        => $order->order_no,
                  'sponsor_id'      => $order->sponsor_id,
                  'total_price'     => $order->total_price,
                  'payment_status'  => 1
                ]
            );    
            //update has_child
             OrderDt::where('order_id', $order->order_id)
                     ->where('child_id', $order->child_id)
                     ->update(['has_child' => 1]);

            $newstartdate=Carbon::parse($order->end_order_date);
            $insertorderdt = new OrderDt();
            $insertorderdt->parent_order_dt_id   = $order->order_dt_id;
            $insertorderdt->child_id             = $order->child_id;
            $insertorderdt->order_id             = $lastorderId;
            $insertorderdt->price                = $order->price;
            $insertorderdt->monthly_subscription = $order->monthly_subscription;
            $insertorderdt->start_order_date     = $newstartdate;
            $insertorderdt->end_order_date       = $newstartdate->copy()->addMonthsNoOverflow($order->monthly_subscription);
            
            $insertorderdt->save();

}


     }

}