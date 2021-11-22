<?php

namespace App\Http\Controllers;

use App\Models\OrderProject;
use App\Models\Sponsor;
use App\Services\Midtrans\CreateSnapTokenForProjectService;
use App\Services\Midtrans\CreateSnapTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProjectOrderController extends Controller
{
    //
    public function index(Request $request){
        $email  = session('key');

        if(session('key')==null){
        
            return redirect()->back()->with(['error' => 'Anda Harus Login Dulu !!']);
        
        }else{

            $sponsor= Sponsor::where('email',$email)->get()->first();
            $idsponsor= $sponsor->sponsor_id;

            do {
                $code = random_int(100000, 999999);
            } while (OrderProject::where("order_project_id", "=", $code)->first());
    
            // save table order_project
             $OrderId = DB::table('order_project')->insertGetId(
                 [
                     'order_project_no'=> $code,
                     'sponsor_id'      => $idsponsor,
                     'project_id'      =>$request->projectid,
                     'price'           => $request->total,
                     'payment_status'  => 1,
                     'created_at'      => Carbon::now(),
                 ]
             );

             $Snaptokenorder = DB::table('order_project')->where('order_project.order_project_id',$OrderId)
             ->join('sponsor_master as sm','sm.sponsor_id','=','order_project.sponsor_id')
             ->join('project_master as pm','pm.project_id','=','order_project.project_id')
             ->get();
            
             $order = OrderProject::where('order_project_id',$OrderId)->first();                        
             $midtrans = new CreateSnapTokenForProjectService($Snaptokenorder,$code);
             $snapToken = $midtrans->getSnapToken();
             $order->snap_token = $snapToken;
             $order->save();
            
            return  Redirect::route('orderprojectcheckout',array('snap_token' => $snapToken,'code' => $code));
         }
     }
     public function orderproject($snapToken, $code){
            $data['order'] = OrderProject::where('order_project_no',$code)->first();
            $data['snapToken'] = $snapToken;

            return view('projectshowpayment',$data);
     }
}