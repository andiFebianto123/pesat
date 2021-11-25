<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailDlp extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        //
        $this->mailData = $mailData;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
     //  dd($this->mailData);
        return $this->markdown('Email.SendDlp')
                ->subject('Pesat - Data Laporan Perkembangan')
                ->attach(public_path('storage/'.$this->mailData['filedlp']))
               // ->with('MailData',$this->mailData);
               ->with(['data'=>$this->mailData]);
                
    }
}
