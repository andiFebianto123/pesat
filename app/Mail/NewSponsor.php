<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSponsor extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $sponsorname;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $sponsorname)
    {
        $this->title = $title;
        $this->sponsorname = $sponsorname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@rectmedia.id')->markdown('Email.new_sponsor')->subject($this->title)
            ->with([
                'title' => $this->title,
                'sponsor' => $this->sponsorname,
            ]);;
    }
}
