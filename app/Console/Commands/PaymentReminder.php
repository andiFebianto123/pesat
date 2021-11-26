<?php

namespace App\Console\Commands;

use PDF;
use App\Models\OrderDt;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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

        $now=Carbon::now();
        $dateafter2weeks= $now->copy()->addDay(14);
        $orders = DB::table('order_hd')
            ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
            ->Join('sponsor_master as sm','order_hd.sponsor_id','=','sm.sponsor_id')
            ->join('child_master as cm','cm.child_id','=','odt.child_id')
            ->where('odt.has_child',0)
            ->where('odt.start_order_date','<=',$dateafter2weeks)
            ->where('odt.has_remind',0)
            ->where('payment_status',1)
            ->where('order_hd.deleted_at',null)
            ->where('odt.deleted_at',null)
            ->whereNotNull('order_hd.parent_order_id')
            ->addSelect('order_hd.order_id')
            ->addSelect('odt.order_dt_id')
            ->addSelect('order_hd.parent_order_id')
            ->addSelect('order_hd.order_no')
            ->addSelect('order_hd.total_price')
            ->addSelect('order_hd.payment_status')
            ->addSelect('odt.parent_order_dt_id')
            ->addSelect('odt.price')
            ->addSelect('odt.monthly_subscription')
            ->addSelect('odt.start_order_date')
            ->addSelect('odt.end_order_date')
            ->addSelect('sm.sponsor_id')
            ->addSelect('sm.full_name as sponsor_name')
            ->addSelect('sm.email')
            ->addSelect('sm.address as sponsor_address')
            ->addSelect('sm.no_hp')
            ->addSelect('cm.child_id')
            ->addSelect('cm.full_name as child_name')
            ->addSelect('cm.registration_number')
            ->addSelect('cm.gender')
            ->addSelect('cm.date_of_birth')
            ->addSelect('cm.class')
            ->addSelect('cm.school')
            ->addSelect('cm.school_year')
            ->get();

            foreach($orders as $key =>$order){


//                    update has_child
                OrderDt::where('order_id', $order->order_id)
                        ->where('child_id', $order->child_id)
                        ->update(['has_remind' => 1]);
                $datenow = Carbon::now();
                $formatdatenow = date('Y-m-d', strtotime($datenow));
                $data["email"] = $order->email;
                $data["title"] = "Peringatan";
                $data["body"] = "-";
                $data["order_id"] = $order->order_id;
                $data["child_name"]= $order->child_name;
                $data["sponsor_name"]= $order->sponsor_name;
                $data["total_price"]= $order->total_price;
                $data["price"]= $order->price;
                $data["monthly_subscription"]= $order->monthly_subscription;
                $data["sponsor_address"]= $order->sponsor_address;
                $data["no_hp"]      = $order->no_hp;
                $data["date_now"]  =$formatdatenow;
                  
                $pdf = PDF::loadView('Email.PaymentReminder', $data);
                  
                Mail::send('Email.BodyPaymentReminder', $data, function($message)use($data, $pdf) {
                      $message->to($data["email"], $data["email"])
                                    ->subject($data["title"])
                                    ->attachData($pdf->output(), $data['order_id']."_".$data['sponsor_name'].".pdf");
                        });
    

    }  

        $now=Carbon::now();
        $dateafter3days= $now->copy()->addDay(4);
        $orders1month = DB::table('order_hd')
        ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
        ->Join('sponsor_master as sm','order_hd.sponsor_id','=','sm.sponsor_id')
        ->join('child_master as cm','cm.child_id','=','odt.child_id')
        ->where('odt.has_child',0)
        ->where('odt.start_order_date','<=',$dateafter3days)
        ->where('odt.has_remind',0)
        ->where('payment_status',1)
        ->where('order_hd.deleted_at',null)
        ->where('odt.deleted_at',null)
        ->whereNotNull('order_hd.parent_order_id')
        ->addSelect('order_hd.order_id')
        ->addSelect('odt.order_dt_id')
        ->addSelect('order_hd.parent_order_id')
        ->addSelect('order_hd.order_no')
        ->addSelect('order_hd.total_price')
        ->addSelect('order_hd.payment_status')
        ->addSelect('odt.parent_order_dt_id')
        ->addSelect('odt.price')
        ->addSelect('odt.monthly_subscription')
        ->addSelect('odt.start_order_date')
        ->addSelect('odt.end_order_date')
        ->addSelect('sm.sponsor_id')
        ->addSelect('sm.full_name as sponsor_name')
        ->addSelect('sm.email')
        ->addSelect('sm.address as sponsor_address')
        ->addSelect('sm.no_hp')
        ->addSelect('cm.child_id')
        ->addSelect('cm.full_name as child_name')
        ->addSelect('cm.registration_number')
        ->addSelect('cm.gender')
        ->addSelect('cm.date_of_birth')
        ->addSelect('cm.class')
        ->addSelect('cm.school')
        ->addSelect('cm.school_year')
        ->get();

    foreach($orders1month as $key =>$order){


        //                    update has_child
                        OrderDt::where('order_id', $order->order_id)
                                ->where('child_id', $order->child_id)
                                ->update(['has_remind' => 1]);

                        $datenow = Carbon::now();
                        $formatdatenow = date('Y-m-d', strtotime($datenow));
                                
                        $data["email"] = $order->email;
                        $data["title"] = "Peringatan";
                        $data["body"] = "-";
                        $data["order_id"]= $order->order_id;
                        $data["no_hp"]  =$order->no_hp;
                        $data["child_name"]= $order->child_name;
                        $data["sponsor_name"]= $order->sponsor_name;
                        $data["monthly_subscription"]= $order->monthly_subscription;
                        $data["sponsor_address"]= $order->sponsor_address;
                        $data["total_price"]= $order->total_price;
                        $data["price"]= $order->price;
                        $data["date_now"]  =$formatdatenow;
                                  
                        $pdf = PDF::loadView('Email.PaymentReminder', $data);
                                  
                        Mail::send('Email.BodyPaymentReminder', $data, function($message)use($data, $pdf) {
                                    $message->to($data["email"], $data["email"])
                                                    ->subject($data["title"])
                                                    ->attachData($pdf->output(), $data['order_id']."_".$data['sponsor_name'].".pdf");
                                    });
        
            }  

    }
}
