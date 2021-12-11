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

        $now=Carbon::now();
        $dateafter2weeks= $now->copy()->addDay(14);
        $orders = DB::table('order_hd')
            ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
            ->where('odt.has_child',0)
            ->where('odt.start_order_date','<=',$dateafter2weeks)
            ->where('odt.has_remind',0)
            ->where('payment_status',1)
            ->where('order_hd.deleted_at',null)
            ->where('odt.deleted_at',null)
            ->get();

            foreach($orders as $key =>$order){


//                    update has_child
                OrderDt::where('order_id', $order->order_id)
                        ->where('child_id', $order->child_id)
                        ->update(['has_remind' => 1]);
    

    }  

        $now=Carbon::now();
        $dateafter3days= $now->copy()->addDay(4);
        $orders1month = DB::table('order_hd')
        ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
        ->where('odt.has_child',0)
        ->where('odt.start_order_date','<=',$dateafter3days)
        ->where('odt.has_remind',0)
        ->where('payment_status',1)
        ->where('order_hd.deleted_at',null)
        ->where('odt.deleted_at',null)
        ->get();

    foreach($orders1month as $key =>$order){


        //                    update has_child
                        OrderDt::where('order_id', $order->order_id)
                                ->where('child_id', $order->child_id)
                                ->update(['has_remind' => 1]);
            
        
            }  

    }
}
