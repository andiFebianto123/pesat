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


       $getEmail= DB::table('order_hd')
            ->Join('order_dt as odt','order_hd.order_id','=','odt.order_id')
            ->Join('sponsor_master as sm','sm.sponsor_id','=','order_hd.sponsor_id')
            ->Join('child_master as cm','cm.child_id','=','odt.child_id')
            ->Join('dlp as dl','dl.child_id','=','cm.child_id')
            ->where('order_hd.deleted_at',null)
            ->where('dl.dlp_id',$id)
            ->orderBy('order_hd.order_id','desc')
            ->first();


        $cekfile = Dlp::where('dlp_id', $id)->first();
        $file= $cekfile->file_dlp;
        $email = $getEmail->email;

        $emailData = [
            'title'     => 'Data Laporan Perkembangan',
            'email'     => $email,
            'filedlp'   => $file,
            'nama'      => 'test'
        ];

        Mail::to($emailData['email'])->send(new SendEmailDlp($emailData));


      \Alert::add('success', 'Email was successfully sent')->flash();

      return back()->withMessage(['message' => 'email was successfully sent']);
    }
}
