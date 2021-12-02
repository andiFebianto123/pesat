<?php

namespace App\Http\Controllers;

use App\Models\HistoryStatusPayment;
use App\Models\OrderHd;
use App\Models\OrderProject;
use Illuminate\Support\Facades\DB;

class MidtransController extends Controller
{
    //
    public function notification()
    {
        \Midtrans\Config::$isProduction = config('midtrans.is_production');;
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;
        $transactiontime = $notif->transaction_time;
        $transactionid  = $notif->transaction_id;
        $transactionmessage = $notif->status_message;
        $statuscode         = $notif->status_code;
        $paymenttype        = $notif->payment_type;
        $paymentamount      = $notif->payment_amounts;
        $merchantid         = $notif->merchant_id;
        $grossamount        = $notif->gross_amount;

        $array_temp = array();
        $array_temp_notif["order_id"] = $order_id;
        $array_temp_notif["transaction_status"] = $transaction;
        $array_temp_notif["transaction_time"] = $transactiontime;
        $array_temp_notif["transaction_id"] = $transactionid;
        $array_temp_notif["status_message"] = $transactionmessage;
        $array_temp_notif["status_code"] = $statuscode;
        $array_temp_notif["payment_type"] = $paymenttype;
        $array_temp_notif["payment_amounts"] = $paymentamount;
        $array_temp_notif["merchant_id"] = $merchantid;
        $array_temp_notif["gross_amount"] = $grossamount;

        array_push($array_temp, $array_temp_notif);

        $arraynotif = json_encode($array_temp);

        DB::table('history_status_payment')->insert([
            'detail_history' => $arraynotif,
        ]);
        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {

            $cekType = substr($order_id,-6);
            $idproyek      = substr($order_id,0,-7);
            $idanak        = substr($order_id,0,-5);
            if($cekType == 'proyek'){

               OrderProject::where('order_project_id', $idproyek)
                            ->update(['status_midtrans' => $transaction,
                                      'payment_status'  => 2,
                                      'payment_type'    => $type
                                    ]);
            }else{

                OrderHd::where('order_id',$idanak)
                        ->update([
                                    'status_midtrans'   => $transaction,
                                    'payment_status'    => 2,
                                    'payment_type'    => $type 
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
            $cekType = substr($order_id,-6);
            $idproyek      = substr($order_id,0,-7);
            $idanak        = substr($order_id,0,-5);
            if($cekType == 'proyek'){

               OrderProject::where('order_project_id', $idproyek)
                            ->update(['status_midtrans' => $transaction,
                                      'payment_status'  => 2,
                                      'payment_type'    => $type
                                    ]);
            }else{

                OrderHd::where('order_id',$idanak)
                        ->update([
                                    'status_midtrans'   => $transaction,
                                    'payment_status'    => 2,
                                    'payment_type'      => $type
                                ]);
            }

            echo "(settlement)".$cekType." Transaction order_id: " . $order_id . " successfully transfered using " . $type;
        } else if ($transaction == 'pending') {
            $cekType = substr($order_id,-6);
            $idproyek      = substr($order_id,0,-7);
            $idanak        = substr($order_id,0,-5);

            if($cekType == 'proyek'){

                OrderProject::where('order_project_id', $idproyek)
                             ->update(['status_midtrans' => $transaction,
                                       'payment_status'  => 1,
                                       'payment_type'    => $type
                                     ]);
             }else{
                OrderHd::where('order_id',$idanak)
                        ->update(['status_midtrans' => $transaction,
                                  'payment_status'  => 1,
                                  'payment_type'    => $type
                                ]);
             }
            // TODO set payment status in merchant's database to 'Pending'
            echo "(pending) Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
        } else if ($transaction == 'deny') {
            
            $cekType = substr($order_id,-6);
            $idproyek      = substr($order_id,0,-7);
            $idanak        = substr($order_id,0,-5);

            if($cekType == 'proyek'){

                OrderProject::where('order_project_id', $idproyek)
                             ->update(['status_midtrans' => $transaction,
                                       'payment_status'  => 3,
                                       'payment_type'    => $type
                                     ]);
             }else{
                OrderHd::where('order_id',$idanak)
                        ->update(['status_midtrans' => $transaction,
                                  'payment_status'  => 3,
                                  'payment_type'    => $type

                                ]);
             }
            // TODO set payment status in merchant's database to 'Denied'
            echo "(deny) Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        } else if ($transaction == 'expire') {
            
            $cekType = substr($order_id,-6);
            $idproyek      = substr($order_id,0,-7);
            $idanak        = substr($order_id,0,-5);

            if($cekType == 'proyek'){

                OrderProject::where('order_project_id', $idproyek)
                             ->update(['status_midtrans' => $transaction,
                                       'payment_status'  => 3,
                                       'payment_type'    => $type
                                     ]);
             }else{
                OrderHd::where('order_id',$idanak)
                        ->update(['status_midtrans' => $transaction,
                                  'payment_status'  => 3,
                                  'payment_type'    => $type
                                ]);
             }
            // TODO set payment status in merchant's database to 'expire'
            echo "(expire) Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        } else if ($transaction == 'cancel') {

            $cekType = substr($order_id,-6);
            $idproyek      = substr($order_id,0,-7);
            $idanak        = substr($order_id,0,-5);

            if($cekType == 'proyek'){

                OrderProject::where('order_project_id', $idproyek)
                             ->update(['status_midtrans' => $transaction,
                                       'payment_status'  => 3,
                                       'payment_type'    => $type
                                     ]);
             }else{
                OrderHd::where('order_id',$idanak)
                        ->update(['status_midtrans' => $transaction,
                                  'payment_status'  => 3,
                                  'payment_type'    => $type
                                ]);
             }
            // TODO set payment status in merchant's database to 'Denied'
            echo "(cancel) Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        }


        return response()->json('');

    }

}
