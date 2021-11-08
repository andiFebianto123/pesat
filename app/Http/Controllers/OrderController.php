<?php

namespace App\Http\Controllers;

use App\Models\ChildMaster;
use App\Models\OrderDt;
use App\Models\OrderHd;
use App\Models\Sponsor;
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            $orders = new OrderDt();
            $orders->order_id           = $OrderId;
            $orders->child_id           = $request->childid;
            $orders->price              = $totalprice;
            $orders->monthly_subscription= $request->monthly_subs;
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

            $data['order'] = OrderHd::where('order_no',$code)->first();
            $data['snapToken'] = $snapToken;

            return view('showpayment',$data);

        }


    }
}
