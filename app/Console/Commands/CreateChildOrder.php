<?php

namespace App\Console\Commands;
use PDF;
use App\Http\Controllers\ReminderOrder;
use App\Mail\ReminderNewOrder;
use App\Models\OrderDt;
use App\Models\OrderHd;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        ->join('sponsor_master as sm','sm.sponsor_id','=','order_hd.sponsor_id')
        ->join('child_master as cm','cm.child_id','=','odt.child_id')
        ->where('odt.has_child',0)
        ->where('odt.end_order_date','<=',$dateafteronemont)
        ->where('payment_status',2)
        ->where('order_hd.deleted_at',null)
        ->where('odt.deleted_at',null)
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

            //do {
            //    $code = random_int(100000, 999999);
           // } while (OrderHd::where("order_no", "=", $code)->first());
    
    
                    $lastorderId = DB::table('order_hd')->insertGetId(
                        [ 'parent_order_id' => $order->order_id,
             //             'order_no'        => $code,//$order->order_no,
                          'sponsor_id'      => $order->sponsor_id,
                          'total_price'     => $order->total_price,
                          'payment_status'  => 1,
                          'created_at'      => Carbon::now()
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
                    
                    $datenow = Carbon::now();
                    $formatdatenow = date('Y-m-d', strtotime($datenow));

                    $data["email"] = $order->email;
                    $data["title"] = "Reminder for Your Subscription";
                    $data["body"] = "This is Demo";
                    $data["sponsor_name"] = $order->sponsor_name;
                    $data["order_id"] = $order->order_id;
                    $data["sponsor_address"] = $order->sponsor_address;
                    $data["no_hp"] = $order->no_hp;
                    $data["child_name"]=$order->child_name;
                    $data["monthly_subscription"]=$order->monthly_subscription;
                    $data["price"]  = $order->price;
                    $data["total_price"]  = $order->total_price;
                    $data["date_now"]   = $formatdatenow;
         
                    $pdf = PDF::loadView('Email.NewOrder', $data);
         
                    Mail::send('Email.BodyNewOrder', $data, function($message)use($data, $pdf) {
                   $message->to($data["email"], $data["email"])
                           ->subject($data["title"])
                           ->attachData($pdf->output(), $data["order_id"]."_".$data["sponsor_name"].".pdf");
               });
        
                }


        $now=Carbon::now();
        $dateafteroneweek= $now->copy()->addDay(7);
        $orders1month = DB::table('order_hd')
        ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
        ->join('sponsor_master as sm','sm.sponsor_id','=','order_hd.sponsor_id')
        ->join('child_master as cm','cm.child_id','=','odt.child_id')
        ->where('odt.has_child',0)
        ->where('odt.end_order_date','<=',$dateafteroneweek)
        ->where('payment_status',2)
        ->where('order_hd.deleted_at',null)
        ->where('odt.deleted_at',null)
        ->addSelect('order_hd.order_id')
        ->addSelect('order_hd.parent_order_id')
        ->addSelect('order_hd.order_no')
        ->addSelect('order_hd.total_price')
        ->addSelect('order_hd.payment_status')
        ->addSelect('odt.order_dt_id')
        ->addSelect('odt.parent_order_dt_id')
        ->addSelect('odt.price')
        ->addSelect('odt.monthly_subscription')
        ->addSelect('odt.start_order_date')
        ->addSelect('odt.end_order_date')
        ->addSelect('sm.sponsor_id')
        ->addSelect('sm.full_name as sponsor_name')
        ->addSelect('sm.address as sponsor_address')
        ->addSelect('sm.email')
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

           // do {
           //     $code = random_int(100000, 999999);
          //  } while (OrderHd::where("order_no", "=", $code)->first());
    
            $lastorderId = DB::table('order_hd')->insertGetId(
                [ 'parent_order_id' => $order->order_id,
            //      'order_no'        => $code,//$order->order_no,
                  'sponsor_id'      => $order->sponsor_id,
                  'total_price'     => $order->total_price,
                  'payment_status'  => 1,
                  'created_at'      => Carbon::now()
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

            $datenow = Carbon::now();
            $formatdatenow = date('Y-m-d', strtotime($datenow));

            $data["email"]       = $order->email;
            $data["title"]       = "Reminder for Your Subscription";
            $data["body"]        = "This is Demo";
            $data["sponsor_name"]= $order->sponsor_name;
            $data["sponsor_address"]= $order->sponsor_address;
            $data["no_hp"]      = $order->no_hp;
            $data["order_no"]      = $order->order_no;
            $data["child_name"]=$order->child_name;
            $data["monthly_subscription"] = $order->monthly_subscription;
            $data["price"] = $order->price;
            $data["total_price"] = $order->total_price;
            $data["date_now"]   = $formatdatenow;
 
            $pdf = PDF::loadView('Email.NewOrder', $data);
 
            Mail::send('Email.BodyNewOrder', $data, function($message)use($data, $pdf) {
           $message->to($data["email"], $data["email"])
                   ->subject($data["title"])
                   ->attachData($pdf->output(),  $data["order_id"]."_".$data["sponsor_name"].".pdf");
       });
 

    }


     }

}