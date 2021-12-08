<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDemo;
use App\Mail\SendEmailDlp;
use App\Models\Dlp;
use Exception;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
class SendDlp extends Controller
{
    //
    public function sendEmail($id){

        $getchild = Dlp::where('dlp_id',$id)
                    ->join('child_master as cm','cm.child_id','=','dlp.child_id')
                     ->first();

if($getchild !== null){

    if($getchild->is_sponsored == 1){


        $getEmail = DB::table('child_master')
                        ->join('order_hd as ohd','ohd.order_id','=','child_master.current_order_id')
                        ->join('sponsor_master as sm','sm.sponsor_id','=','ohd.sponsor_id')
                        ->join('dlp as dl','dl.child_id','=','child_master.child_id')
                        ->where('dl.dlp_id',$id)
                        ->select('ohd.*', 'dl.*','child_master.full_name as child_name','sm.full_name as sponsor_name','sm.email')
                        ->first();

        $cekfile = Dlp::where('dlp_id', $id)->first();
        $file= $cekfile->file_dlp;
        $email = $getEmail->email;
        $childname = $getEmail->child_name;
        $sponsorname = $getEmail->sponsor_name;

        $emailData = [
            'title'     => 'Data Laporan Perkembangan',
            'email'     => $email,
            'filedlp'   => $file,
            'child_name'      => $childname,
            'sponsor_name'      => $sponsorname
        ];
        

        try {
            // Validate the value...
            Mail::to($emailData['email'])->send(new SendEmailDlp($emailData));
            
            Dlp::where('dlp_id', $id)
            ->update(['deliv_status' => 2]);

            \Alert::add('success', 'Email was successfully sent')->flash();
            return back()->withMessage(['message' => 'email was successfully sent']);
        } catch (Exception $e) {
            report($e);
            
            Dlp::where('dlp_id', $id)
            ->update(['deliv_status' => 3]);

            \Alert::add('error', 'Email was failed to sent')->flash();

            return back()->withMessage(['message' => 'email was successfully sent']);//false;
        
        }

    

    }else{
        \Alert::add('error', 'The child dont have a sponsor')->flash();

        return back()->withMessage(['message' => 'The child dont have a sponsor']);

    }
    }else{
        \Alert::add('error', 'Data tidak ditemukan')->flash();

        return back()->withMessage(['message' => 'Data tidak ditemukan']);       
    }   
}
}