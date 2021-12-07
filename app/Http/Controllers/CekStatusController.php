<?php

namespace App\Http\Controllers;

use App\Models\DataOrder;
use App\Models\OrderHd;
use App\Models\OrderProject;
use Illuminate\Support\Facades\DB;

class CekStatusController extends Controller
{
    //
    public function index($id)
    {

        $curl = curl_init();
        $idproyek = $id . "-proyek";

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/" . $idproyek . "/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "\n\n",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic U0ItTWlkLXNlcnZlci1oU0ZrQnBzRi02cHBKRkRmOTNxdFZLRGc6",
            ),
        ));

        $response = curl_exec($curl);
        $decoderespon = json_decode($response, true);
        curl_close($curl);

        $transaction = $decoderespon['transaction_status'];
        $type = $decoderespon['payment_type'];
        $order_id = $decoderespon['order_id'];

        $idproyek = substr($order_id, 0, -7);

        $getStatus = OrderProject::where('order_project_id', $id)->first();

        if ($getStatus->payment_status == 2) {
            \Alert::add('success', 'Proses pembayaran sudah sukses')->flash();
            return back()->withMessage(['message' => 'Proses pembayaran sudah sukses']);
        } else {

            if ($transaction == 'capture') {
                // For credit card transaction, we need to check whether transaction is challenge by FDS or not
                if ($type == 'credit_card') {

                    DB::table('project_history_status_payment')->insert([
                        'detail_history' => $response,
                        'status' => 2,
                        'user_id' => backpack_user()->id,
                        'status_midtrans' => $transaction,

                    ]);

                    OrderProject::where('order_project_id', $idproyek)
                        ->update(['status_midtrans' => $transaction,
                            'payment_status' => 2,
                            'payment_type' => $type,
                        ]);

                 //   echo "(capture) Transaction order_id: " . $order_id . " successfully captured using " . $type;
                 \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                 return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

                }
            } else if ($transaction == 'settlement') {
                // TODO set payment status in merchant's database to 'Settlement'

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 2,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);

                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 2,
                        'payment_type' => $type,
                    ]);

                //             echo "(settlement)" . $cekType . " Transaction order_id: " . $order_id . " successfully transfered using " . $type;
                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

            } else if ($transaction == 'pending') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 1,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);

                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 1,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'Pending'
                //     echo "(pending) Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
                \Alert::add('error', 'Tidak ada status yang diperbarui')->flash();
                return back()->withMessage(['message' => 'Tidak ada status yang diperbarui']);

            } else if ($transaction == 'deny') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);

                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
                // TODO set payment status in merchant's database to 'Denied'
//                echo "(deny) Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

            } else if ($transaction == 'expire') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);
                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'expire'
//                echo "(expire) Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

            } else if ($transaction == 'cancel') {

                DB::table('project_history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,

                ]);

                OrderProject::where('order_project_id', $idproyek)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'Denied'
//                echo "(cancel) Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
                    \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                    return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

            }

            return response()->json('');

        }

    }
    public function childcekstatus($id)
    {

        $curl = curl_init();
        $idanak = $id . "-anak";

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/" . $idanak . "/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "\n\n",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic U0ItTWlkLXNlcnZlci1oU0ZrQnBzRi02cHBKRkRmOTNxdFZLRGc6",
            ),
        ));


        $response = curl_exec($curl);
        $decoderespon = json_decode($response, true);
        curl_close($curl);
        
        $statuscode = $decoderespon['status_code'];
        $getStatus = DataOrder::where('order_id', $id)->first();
        
        if($statuscode=='404'){

            \Alert::add('error', 'Data pembayaran tidak ditemukan')->flash();
            return back()->withMessage(['message' => 'Data pembayaran tidak ditemukan']);

        }else{

        if ($getStatus->payment_status == 2) {

            \Alert::add('success', 'Proses pembayaran sudah sukses')->flash();
            return back()->withMessage(['message' => 'Proses pembayaran sudah sukses']);

        } else {

            $transaction = $decoderespon['transaction_status'];
            $type = $decoderespon['payment_type'];
            $order_id = $decoderespon['order_id'];
    
            $idanak = substr($order_id, 0, -5);

            if ($transaction == 'capture') {
                // For credit card transaction, we need to check whether transaction is challenge by FDS or not
                if ($type == 'credit_card') {

                    DB::table('history_status_payment')->insert([
                        'detail_history' => $response,
                        'status' => 2,
                        'status_midtrans' => $transaction,
                        'user_id' => backpack_user()->id,
                    ]);

                    OrderHd::where('order_id', $idanak)
                        ->update([
                            'status_midtrans' => $transaction,
                            'payment_status' => 2,
                            'payment_type' => $type,
                        ]);

//                    echo "(capture) Transaction order_id: " . $order_id . " successfully captured using " . $type;
                        \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                        return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);


                }
            } else if ($transaction == 'settlement') {
                // TODO set payment status in merchant's database to 'Settlement'

                DB::table('history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 2,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,
                ]);

                OrderHd::where('order_id', $idanak)
                    ->update([
                        'status_midtrans' => $transaction,
                        'payment_status' => 2,
                        'payment_type' => $type,
                    ]);

                //             echo "(settlement)" . $cekType . " Transaction order_id: " . $order_id . " successfully transfered using " . $type;
                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

            } else if ($transaction == 'pending') {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 1,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,
                ]);

                OrderHd::where('order_id', $idanak)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 1,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'Pending'
                //     echo "(pending) Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
                \Alert::add('error', 'Tidak ada status yang diperbarui')->flash();
                return back()->withMessage(['message' => 'Tidak ada status yang diperbarui']);

            } else if ($transaction == 'deny') {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,
                ]);

                OrderHd::where('order_id', $idanak)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,

                    ]);

                // TODO set payment status in merchant's database to 'Denied'
//                echo "(deny) Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

            } else if ($transaction == 'expire') {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,
                ]);
                OrderHd::where('order_id', $idanak)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);

                // TODO set payment status in merchant's database to 'expire'
            //    echo "(expire) Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

            } else if ($transaction == 'cancel') {

                DB::table('history_status_payment')->insert([
                    'detail_history' => $response,
                    'status' => 3,
                    'status_midtrans' => $transaction,
                    'user_id' => backpack_user()->id,
                ]);

                OrderHd::where('order_id', $idanak)
                    ->update(['status_midtrans' => $transaction,
                        'payment_status' => 3,
                        'payment_type' => $type,
                    ]);
            }
            // TODO set payment status in merchant's database to 'Denied'
        //    echo "(cancel) Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
            \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
            return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

        }

        return response()->json('');
    }

    }
}
