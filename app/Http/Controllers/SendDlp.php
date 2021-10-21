<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDemo;
use App\Mail\SendEmailDlp;
use App\Models\Dlp;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
class SendDlp extends Controller
{
    //
    public function sendEmail($id){


        $cekfile = Dlp::where('dlp_id', $id)->first();
        $file= $cekfile->file_dlp;
        $email = 'michaelmurbianto@gmail.com';

        $emailData = [
            'title'     => 'Data Laporan Perkembangan',
            'email'     => $email,
            'filedlp'   => $file
        ];

        Mail::to($emailData['email'])->send(new SendEmailDlp($emailData));


      \Alert::add('success', 'Email was successfully sent')->flash();

      return back()->withMessage(['message' => 'email was successfully sent']);
    }
}
