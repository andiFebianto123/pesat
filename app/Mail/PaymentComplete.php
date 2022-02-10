<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Barryvdh\DomPDF\Facade as PDF;

class PaymentComplete extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $orderDetails;
    public $emailSubject;
    public $title;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $orderDetails, $emailSubject, $title)
    {
        $this->order = $order;
        $this->orderDetails = $orderDetails;
        $this->emailSubject = $emailSubject;
        $this->title = $title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
	    $pdf = PDF::loadView('pdf_order', ['order' => $this->order, 'orderDetails' => $this->orderDetails, 'typePdf' => 'Receipt']);
        return $this->markdown('Email.payment_complete')
        ->attachData($pdf->output(), 'Receipt #' . $this->order->order_id . '.pdf', [
            'mime' => 'application/pdf',
        ])->subject($this->emailSubject)
        ->with([
            'title' => $this->title, 
            'sponsor' => $this->order->sponsorname, 
            'date' => Carbon::parse($this->order->created_at)->format('F d, Y'), 
            'titleOrder' => '[Donation #' . $this->order->order_id . '] (' .  Carbon::parse($this->order->created_at)->format('F d, Y') . ')', 
            'subtotal' => number_format($this->order->total_price, 2, ',', '.'),
            'total' => number_format($this->order->total_price, 2, ',', '.'),
            'payment_method' => $this->order->payment_type,
            'orderDetails' => $this->orderDetails
        ]);
    }
}
