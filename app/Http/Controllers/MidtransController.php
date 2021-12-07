<?php

namespace App\Http\Controllers;

use App\Models\OrderHd;
use App\Models\OrderProject;
use Illuminate\Support\Facades\DB;

class MidtransController extends Controller
{
    //
    public function notification()
    {
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        $arraynotif = json_encode($notif->getResponse());

        $cekType = substr($order_id, -6);
        $idproyek = substr($order_id, 0, -7);
        $idanak = substr($order_id, 0, -5);

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {

                if ($cekType == 'proyek') {

                    DB::table('project_history_status_payment')->insert([
                        'detail_history' => $arraynotif,
                        'status'        =>2,
                        'status_midtrans' => $transaction,
        
                    ]);
    

                    OrderProject::where('order_project_id', $idproyek)
                        ->update(['status_midtrans' => $transaction,
                            'payment_status' => 2,
                            'payment_type' => $type,
                        ]);
                } else {

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $arraynotif,
                        'status'          =>2,
                        'status_midtrans' => $transaction,
                    ]);

                    OrderHd::where('order_id', $idanak)
                        ->update([
                            'status_midtrans' => $transaction,
                            'payment_status' => 2,
                            'payment_type' => $type,
                        ]);
                }
                echo "(capture) Transaction order_id: " . $order_id . " successfully captured using " . $type;

                // if ($fraud == 'challenge') {
                //     // TODO set payment status in merchant's database to 'Challenge by FDS'
                //     // TODO merchant should decide whether this transaction is authorized or not in MAP
                //     echo "Transaction order_id: " . $order_id . " is challenged by FDS";
                // } else {
                //     // TODO set payment status in merchant's database to 'Success'
                //     echo "Transaction order_id: " . $order_id . " successfully captured using " . $type;
                // }
            }
        } else if ($transaction == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'

            if ($cekType == 'proyek') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>2,
                    'status_midtrans' => $transaction,
    
                ]);

                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 2,
                        'payment_type' => $type,
                    ]);
            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'          =>2,
                    'status_midtrans' => $transaction,
                ]);

                OrderHd::where('order_id', $idanak)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 2,
                        'payment_type' => $type,
                    ]);
            }

            echo "(settlement)" . $cekType . " Transaction order_id: " . $order_id . " successfully transfered using " . $type;
        } else if ($transaction == 'pending') {

            if ($cekType == 'proyek') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>1,
                    'status_midtrans' => $transaction,
    
                ]);

                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 1,
                        'payment_type' => $type,
                    ]);
            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'          =>1,
                    'status_midtrans' => $transaction,
                ]);

                OrderHd::where('order_id', $idanak)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 1,
                        'payment_type' => $type,
                    ]);
            }
            // TODO set payment status in merchant's database to 'Pending'
            echo "(pending) Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;

        } else if ($transaction == 'deny') {


            if ($cekType == 'proyek') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>3,
                    'status_midtrans' => $transaction,
    
                ]);

                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'          =>3,
                    'status_midtrans' => $transaction,
                ]);
                OrderHd::where('order_id', $idanak)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,

                    ]);
            }
            // TODO set payment status in merchant's database to 'Denied'
            echo "(deny) Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        } else if ($transaction == 'expire') {


            if ($cekType == 'proyek') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>3,
                    'status_midtrans' => $transaction,
    
                ]);
                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'          =>3,
                    'status_midtrans' => $transaction,
                ]);
                OrderHd::where('order_id', $idanak)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
            }
            // TODO set payment status in merchant's database to 'expire'
            echo "(expire) Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        } else if ($transaction == 'cancel') {

            if ($cekType == 'proyek') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'        =>3,
                    'status_midtrans' => $transaction,
    
                ]);

                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
            } else {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $arraynotif,
                    'status'          =>3,
                    'status_midtrans' => $transaction,
                ]);
                
                OrderHd::where('order_id', $idanak)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
            }
            // TODO set payment status in merchant's database to 'Denied'
            echo "(cancel) Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        }

        return response()->json('');

    }

}
