<?php

namespace App\Http\Controllers;

use App\Models\OrderHd;
use App\Models\OrderProject;
use App\Models\ProjectMaster;

class CekStatusController extends Controller
{
    //
    public function index($id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/" . $id . "/status",
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
        
        $getStatus=OrderProject::where('order_project_id',$id)->first();
        
        if($getStatus->payment_status==2){
            \Alert::add('success', 'Proses pembayaran sudah sukses')->flash();
            return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
        }else{

        if ($decoderespon != null) {
            if ($decoderespon['transaction_status'] == 'settlement') {
                OrderProject::where('order_project_id', $id)
                    ->update(['payment_status' => 2]);

                $getOrder = OrderProject::where('order_project_id',$id)->first();

               // $getProject = ProjectMaster::where('project_id',$getOrder->project_id)->first();

                    $getTotalAmount = OrderProject::groupBy('project_id')
                                     ->where('project_id',$getOrder->project_id)
                                     ->where('payment_status',2)                            
                                     ->selectRaw('sum(price) as sum_price')
                                     ->pluck('sum_price')
                                     ->first();
                     ProjectMaster::where('project_id', $getOrder->project_id)
                                     ->update(['last_amount' => $getTotalAmount]);


                    \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();
                    return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);

                }  
                else {

                    \Alert::add('error', 'Tidak ada status yang diperbarui')->flash();
                    return back()->withMessage(['message' => 'Tidak ada status yang diperbarui']);

            }

        } else {
            \Alert::add('error', 'Gagal memuat status, silahkan coba lagi')->flash();

            return back()->withMessage(['message' => 'Gagal memuat status, silahkan coba lagi']);
        }
    }

    }
    public function childcekstatus($id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/" . $id . "/status",
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

    if($decoderespon != null){

        if ($decoderespon['transaction_status'] == 'settlement') {
            OrderHd::where('order_id', $id)
                ->update(['payment_status' => 2]);

                \Alert::add('success', 'Status pembayaran berhasil diperbarui')->flash();

                return back()->withMessage(['message' => 'Status pembayaran berhasil diperbarui']);
        }else{
          
            \Alert::add('error', 'Tidak ada status yang diperbarui')->flash();

            return back()->withMessage(['message' => 'Tidak ada status yang diperbarui']);
        }

    }else{

        \Alert::add('error', 'Gagal memuat status, silahkan coba lagi')->flash();

        return back()->withMessage(['message' => 'Gagal memuat status, silahkan coba lagi']);
    }
    }
}
