<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDemo;
use App\Mail\SendEmailDlp;
use App\Models\Dlp;
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
    if($getchild->is_sponsored == 1){

       $getEmail= DB::table('order_hd')
            ->Join('order_dt as odt','order_hd.order_id','=','odt.order_id')
            ->Join('sponsor_master as sm','sm.sponsor_id','=','order_hd.sponsor_id')
            ->Join('child_master as cm','cm.child_id','=','odt.child_id')
            ->Join('dlp as dl','dl.child_id','=','cm.child_id')
            ->where('order_hd.deleted_at',null)
            ->where('dl.dlp_id',$id)
            ->select('order_hd.*', 'odt.*','dl.*','cm.full_name as child_name','sm.full_name as sponsor_name','sm.email')
            ->orderBy('order_hd.order_id','desc')
            ->first();
      //  dd($getEmail);

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

        Mail::to($emailData['email'])->send(new SendEmailDlp($emailData));


      \Alert::add('success', 'Email was successfully sent')->flash();

      return back()->withMessage(['message' => 'email was successfully sent']);

    }else{
        \Alert::add('error', 'The child dont have a sponsor')->flash();

        return back()->withMessage(['message' => 'The child dont have a sponsor']);

    }
    }
}
