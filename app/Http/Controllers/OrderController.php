<?php

namespace App\Http\Controllers;

use App\Models\ChildMaster;
use App\Models\OrderDt;
use App\Models\OrderHd;
use App\Models\Sponsor;
use App\Services\Midtrans\CreateSnapTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    //
    public function index(Request $request){
        $user   = auth()->user();
        $email  = session('key');
      
        $totalprice=$request->monthly_subs * 150000;
                   
        do {
            $code = random_int(100000, 999999);
        } while (OrderHd::where("order_no", "=", $code)->first());


        if(session('key')==null){
            return redirect()->back()->with(['error' => 'Anda Harus Login Dulu !!']);
        }else{

            $sponsor= Sponsor::where('email',$email)->get()->first();
            $idsponsor= $sponsor->sponsor_id;
            
            // save table order_hd
            $OrderId = DB::table('order_hd')->insertGetId(
                [
                    'parent_order_id' => null,
                    'order_no'        =>$code,
                    'sponsor_id'      =>$idsponsor,
                    'total_price'     =>$totalprice,
                    'payment_status'  =>1,
                ]
            );
            // save table order_dt
            $startOrderdate = Carbon::now();
            $orders = new OrderDt();
            $orders->order_id           = $OrderId;
            $orders->child_id           = $request->childid;
            $orders->price              = $totalprice;
            $orders->monthly_subscription= $request->monthly_subs;
            $orders->start_order_date   = $startOrderdate;
            $orders->end_order_date     = $startOrderdate->copy()->addMonthsNoOverflow($request->monthly_subs);
            $orders->save();

            $Snaptokenorder = DB::table('order_hd')->where('order_hd.order_id',$OrderId)
            ->join('sponsor_master as sm','sm.sponsor_id','=','order_hd.sponsor_id')
            ->join('order_dt as odt', 'odt.order_id', '=', 'order_hd.order_id')
            ->join('child_master as cm','cm.child_id','=','odt.child_id')
            ->select(
                'order_hd.*', 
                'odt.*', 
                'cm.full_name',
                'sm.full_name as sponsor_name',
                'sm.email',
                'sm.no_hp'
                )
            ->get();
            
            $order = OrderHd::where('order_id',$OrderId)->first();                        
            $midtrans = new CreateSnapTokenService($Snaptokenorder,$code);
            $snapToken = $midtrans->getSnapToken();
            $order->snap_token = $snapToken;
            $order->save();
      
           return  Redirect::route('ordercheckout',array('snap_token' => $snapToken,'code' => $code));
    }
}
    public function orderdonation($snapToken, $code){
            $data['order'] = OrderHd::where('order_no',$code)->first();
            $data['snapToken'] = $snapToken;

            return view('showpayment',$data);

        }

    
public function cekstatus(){

    $datas = OrderHd::where('payment_status',1)->get();
    
    foreach($datas as $data){
        $orderno= $data->order_no;
   
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/".$orderno."/status",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS =>"\n\n",
        CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Content-Type: application/json",
        "Authorization: Basic U0ItTWlkLXNlcnZlci1oU0ZrQnBzRi02cHBKRkRmOTNxdFZLRGc6"
      ),
    ));
    
    $response       = curl_exec($curl);
    $decoderespon   = json_decode($response,true);
    curl_close($curl);


    
    if($decoderespon['status_code']=='200' && $decoderespon['transaction_status']=='settlement'){
        OrderHd::where('order_no', $orderno)
        ->update(['payment_status' => 2]);
    }
    if($decoderespon['status_code']=='407'){
        OrderHd::where('order_no', $orderno)
        ->update(['payment_status' => 3]);
    }


}

}

public function reminderinvoice(){
    
    $orders = DB::table('order_hd')
            ->Join('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
            ->get();      
    

      foreach ($orders as $key => $order) {

        //if($order->monthly_subscription != 1){
        
            $startdate = Carbon::parse($order->start_order_date);
            $enddate   = Carbon::parse($order->end_order_date);
            $interval  = $enddate->diffInDays($startdate);
            ///////////
            $now=Carbon::now();
            $oneweekaftermont = $now->copy()->addDay(7);
            dd($oneweekaftermont);
            $dateafteronemont= $now->copy()->addMonthsNoOverflow();
           
            $intervalneworder = $now->addMonthsNoOverflow(3);
//            dd($intervalneworder);
            //$inetervalremind=
           
            $intervalcreateorder = $startdate->diffInDays($intervalneworder);
            ///////////
            $intervalereminder = $intervalcreateorder + 14;
  //          $now=Carbon::now();

            $intervalnow=$startdate->diffInDays($now);
    //        dd($intervalereminder);
            
            $intervalcreateorder = $interval-7;
     //       dd($interval,$intervalcreateorder);

            // $insertorderhd = new OrderHd();
            // $insertorderhd->parent_order_id = $order->order_id;
            // $insertorderhd->order_no        = $order->order_no;
            // $insertorderhd->sponsor_id      = $order->sponsor_id;
            // $insertorderhd->total_price     = $order->total_price;
            // $insertorderhd->payment_status  = 1;
            // $insertorderhd->save();

            $lastorderId = DB::table('order_hd')->insertGetId(
                [ 'parent_order_id' => $order->order_id,
                  'order_no'        => $order->order_no,
                  'sponsor_id'      => $order->sponsor_id,
                  'total_price'     => $order->total_price,
                  'payment_status'  => 1
                ]
            );   
//dd($lastorderId);

            //update has_child
            OrderDt::where('order_id', $order->order_id)
                    ->where('child_id', $order->child_id)
                    ->update(['has_child' => 1]);

            $newstartdate=Carbon::parse($order->end_order_date);
            $insertorderdt = new OrderDt();
            $insertorderdt->child_id             = $order->child_id;
            $insertorderdt->order_id             = $order->$lastorderId;
            $insertorderdt->price                = $order->price;
            $insertorderdt->monthly_subscription = $order->monthly_subscription;
            $insertorderdt->start_order_date     = $newstartdate;
            $insertorderdt->end_order_date       = $newstartdate->copy()->addMonthsNoOverflow($order->monthly_subscription);
            
            $insertorderdt->save();


            //$jumdays= cal_days_in_month(CAL_GREGORIAN, 11,2021);
            //dd($dateAfterOneMonth);


        //}else{

        
    
        //}
    //     $startdate = Carbon::parse($order->updated_at)->format('Y-m-d H:i:s');
    //     $enddate = Carbon::parse(date('Y-m-d H:i:s', strtotime('+'.$order->monthly_subscription.' month', strtotime($startdate))));
    //     $intervaltotal = $enddate->diffInDays($startdate);
        
    //     $reminder = $intervaltotal-7;
                     
    //     $end = Carbon::parse($order->updated_at)->format('Y-m-d H:i:s');
    //     $now  = Carbon::now();
        
    //     $intervalnow = $now->diffInDays($end);

    //     $totalreminder = DB::table('order_hd')
    //                  ->leftJoin('order_dt as odt', 'order_hd.order_id', '=', 'odt.order_id')
    //                  //->whereNotNull('parent_order_id')
    //                  ->where('parent_order_id',$order->order_id)
    //                  ->get();
                    
    //      if (count($totalreminder) < 1){

    //         if ($intervalnow >= $reminder) {

    //             $insertorders = new OrderHd();
    //             $insertorders->parent_order_id = $order->order_id;
    //             $insertorders->order_no        = $order->order_no;
    //             $insertorders->sponsor_id      = $order->sponsor_id;
    //             $insertorders->total_price     = $order->total_price;
    //             $insertorders->payment_status  = 1;
    //             $insertorders->save();

    //     }
    // }

     }

}

}
