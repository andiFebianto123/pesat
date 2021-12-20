<?php

namespace App\Http\Controllers;

use App\Models\ChildMaster;
use App\Models\DataDetailOrder;
use App\Models\DataOrder;
use App\Models\OrderHd;
use App\Models\OrderProject;
use App\Models\ProjectMaster;
use Exception;
use Illuminate\Support\Facades\DB;

class MidtransController extends Controller
{
    //
    public function notification()
    {
        DB::beginTransaction();

        try{

        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        $arraynotif = json_encode($notif->getResponse());

        $explodeId = explode('-',$order_id);//substr($order_id, -6);
        
        $cekType = $explodeId[0];
        
      //  dd($cekType);
        $orderId = $explodeId[1];//substr($order_id, 0, -7);

        //$orderIdanak = substr($order_id, 0, -5);
       // dd($order_id,$orderIdanak);
        $getProjectId = OrderProject::where('order_project_id',$orderId)->first();
       
        $cekDetailOrder = DataDetailOrder::where('order_id', $orderId)->get();   
    
        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not

                if ($cekType == 'proyek') {

                    $projectId    = $getProjectId->project_id;
                    $amount       = $getProjectId->amount;
                    
                    DB::table('project_history_status_payment')->insert([
                        'detail_history' => $arraynotif,
                        'status'        =>2,
                        'status_midtrans' => $transaction,
        
                    ]);
    

                    OrderProject::where('order_project_id', $orderId)
                        ->update(['status_midtrans' => $transaction,
                            'payment_status' => 2,
                            'payment_type' => $type,
                        ]);

                    $totalPrice = OrderProject::where('project_id', $projectId)
                        ->where('payment_status', 2)
                        ->groupBy('project_id')
                        ->selectRaw('sum(price) as sum_price')
                        ->pluck('sum_price')
                        ->first();
                    
                    $lastamount = ProjectMaster::find($projectId);
                    $lastamount->last_amount = $totalPrice;
                    $lastamount->save();

                    if($totalPrice >= $amount){
                        $lastamount = ProjectMaster::find($projectId);
                        $lastamount->is_closed = 1;
                        $lastamount->save();

                    }
                    

                } else {

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $arraynotif,
                        'status'          =>2,
                        'status_midtrans' => $transaction,
                    ]);

                    DataOrder::where('order_id', $orderId)
                        ->update([
                            'status_midtrans' => $transaction,
                            'payment_status' => 2,
                            'payment_type' => $type,
                        ]);
                }
                echo "(capture) Transaction order_id: " . $order_id . " successfully captured using " . $type;


        } else if ($transaction == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'

            if ($cekType == 'proyek') {

                $projectId    = $getProjectId->project_id;
                $amount       = $getProjectId->amount;

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>2,
                    'status_midtrans' => $transaction,
    
                ]);

                OrderProject::where('order_project_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 2,
                        'payment_type' => $type,
                    ]);
                
                $totalPrice = OrderProject::where('project_id', $projectId)
                    ->where('payment_status', 2)
                    ->groupBy('project_id')
                    ->selectRaw('sum(price) as sum_price')
                    ->pluck('sum_price')
                    ->first();
                
                $lastamount = ProjectMaster::find($projectId);
                $lastamount->last_amount = $totalPrice;
                $lastamount->save();

                if($totalPrice >= $amount){
                    $lastamount = ProjectMaster::find($projectId);
                    $lastamount->is_closed = 1;
                    $lastamount->save();

                }

            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'          =>2,
                    'status_midtrans' => $transaction,
                ]);

                DataOrder::where('order_id', $orderId)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 2,
                        'payment_type' => $type,
                    ]);
            }

            echo "(settlement)" . $cekType . " Transaction order_id: " . $order_id . " successfully transfered using " . $type;
        } else if ($transaction == 'pending') {

         

            if ($cekType == 'proyek') {

                $projectId    = $getProjectId->project_id;
                $amount       = $getProjectId->amount;

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>1,
                    'status_midtrans' => $transaction,
    
                ]);

                OrderProject::where('order_project_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 1,
                        'payment_type' => $type,
                    ]);

                $totalPrice = OrderProject::where('project_id', $projectId)
                    ->where('payment_status', 2)
                    ->groupBy('project_id')
                    ->selectRaw('sum(price) as sum_price')
                    ->pluck('sum_price')
                    ->first();
                
                $lastamount = ProjectMaster::find($projectId);
                $lastamount->last_amount = $totalPrice;
                $lastamount->save();

                if($totalPrice >= $amount){
                    $lastamount = ProjectMaster::find($projectId);
                    $lastamount->is_closed = 1;
                    $lastamount->save();

                }

            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'          =>1,
                    'status_midtrans' => $transaction,
                ]);

                DataOrder::where('order_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 1,
                        'payment_type' => $type,
                    ]);
            }
            // TODO set payment status in merchant's database to 'Pending'
            echo "(pending) Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;

        } else if ($transaction == 'deny') {

            if ($cekType == 'proyek') {

                $projectId    = $getProjectId->project_id;
                $amount       = $getProjectId->amount;
                
                //update child_master (is_sponsored & current_order_id)
                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>3,
                    'status_midtrans' => $transaction,
    
                ]);

                OrderProject::where('order_project_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
                $totalPrice = OrderProject::where('project_id', $projectId)
                    ->where('payment_status', 2)
                    ->groupBy('project_id')
                    ->selectRaw('sum(price) as sum_price')
                    ->pluck('sum_price')
                    ->first();
                
                $lastamount = ProjectMaster::find($projectId);
                $lastamount->last_amount = $totalPrice;
                $lastamount->save();

                if($totalPrice >= $amount){
                    $lastamount = ProjectMaster::find($projectId);
                    $lastamount->is_closed = 1;
                    $lastamount->save();

                }
            } else {

             //   $getChildId    = $cekDetailOrder->child_id;

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'          =>3,
                    'status_midtrans' => $transaction,
                ]);
                DataOrder::where('order_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,

                    ]);
                
                    foreach ($cekDetailOrder as $key => $detailOrder) {
                        
                        $child = ChildMaster::find($detailOrder->child_id);
                        $child->is_sponsored = 0;
                        $child->current_order_id = null;
                        $child->save();

                    }
                    
            }
            // TODO set payment status in merchant's database to 'Denied'
            echo "(deny) Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        } else if ($transaction == 'expire') {


            if ($cekType == 'proyek') {

                $projectId    = $getProjectId->project_id;
                $amount       = $getProjectId->amount;

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>3,
                    'status_midtrans' => $transaction,
    
                ]);
                OrderProject::where('order_project_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);

                $totalPrice = OrderProject::where('project_id', $projectId)
                    ->where('payment_status', 2)
                    ->groupBy('project_id')
                    ->selectRaw('sum(price) as sum_price')
                    ->pluck('sum_price')
                    ->first();
                
                $lastamount = ProjectMaster::find($projectId);
                $lastamount->last_amount = $totalPrice;
                $lastamount->save();

                if($totalPrice >= $amount){
                    $lastamount = ProjectMaster::find($projectId);
                    $lastamount->is_closed = 1;
                    $lastamount->save();

                }

            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status_midtrans' => $transaction,
                ]);
                DataOrder::where('order_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_type' => $type,
                    ]);
                
                    foreach ($cekDetailOrder as $key => $detailOrder) {
                        
                        $child = ChildMaster::find($detailOrder->child_id);
                        $child->is_sponsored = 0;
                        $child->current_order_id = null;
                        $child->save();

                    }

            }
            // TODO set payment status in merchant's database to 'expire'
            echo "(expire) Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        } else if ($transaction == 'cancel') {

            if ($cekType == 'proyek') {

                $projectId    = $getProjectId->project_id;
                $amount       = $getProjectId->amount;

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>3,
                    'status_midtrans' => $transaction,
    
                ]);

                OrderProject::where('order_project_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
            
                $totalPrice = OrderProject::where('project_id', $projectId)
                    ->where('payment_status', 2)
                    ->groupBy('project_id')
                    ->selectRaw('sum(price) as sum_price')
                    ->pluck('sum_price')
                    ->first();
                
                $lastamount = ProjectMaster::find($projectId);
                $lastamount->last_amount = $totalPrice;
                $lastamount->save();

                if($totalPrice >= $amount){
                    $lastamount = ProjectMaster::find($projectId);
                    $lastamount->is_closed = 1;
                    $lastamount->save();

                }

            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'         => 3,
                    'status_midtrans'=> $transaction,
                ]);
                
                DataOrder::where('order_id', $orderId)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);

                foreach ($cekDetailOrder as $key => $detailOrder) {
                        
                        $child = ChildMaster::find($detailOrder->child_id);
                        $child->is_sponsored = 0;
                        $child->current_order_id = null;
                        $child->save();

                    }
            }
            // TODO set payment status in merchant's database to 'Denied'
            echo "(cancel) Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        }

        DB::commit();

        return response()->json('');

    }catch(Exception $e){
        
        DB::rollBack(); 
        throw $e;
    
 }
}
    

}
