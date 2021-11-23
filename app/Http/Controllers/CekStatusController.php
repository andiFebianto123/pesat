<?php

namespace App\Http\Controllers;

use App\Models\OrderProject;
use Illuminate\Http\Request;

class CekStatusController extends Controller
{
    //
    public function index($id){

            $curl = curl_init();
    
            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/".$id."/status",
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
    
      //  dd($decoderespon);
        
        if($decoderespon['transaction_status']=='settlement'){
            OrderProject::where('order_project_no', $id)
            ->update(['payment_status' => 2]);
        }elseif($decoderespon['transaction_status']=='pending'){

            OrderProject::where('order_project_no', $id)
            ->update(['payment_status' => 1]);
        }else{
            OrderProject::where('order_project_no', $id)
            ->update(['payment_status' => 3]);

        }
        \Alert::add('success', 'Status pembayaran sudah di perbarui')->flash();

        return back()->withMessage(['message' => 'Status pembayaran sudah di perbarui']);    
    
    }
}
