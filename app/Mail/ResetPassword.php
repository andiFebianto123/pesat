<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $generatepass;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $generatepass)
    {
        $this->title = $title;
        $this->generatepass = $generatepass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('Email.ResetPassword')->subject($this->title)
            ->with([
                'title' => $this->title,
                'generatepass' => $this->generatepass,
        ]);
    }
}
