<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReminderOrder extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $orderDetails;
    public $emailSubject;
    public $title;
    public $lastReminder;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $orderDetails, $emailSubject, $title, $lastReminder)
    {
        $this->order = $order;
        $this->orderDetails = $orderDetails;
        $this->emailSubject = $emailSubject;
        $this->title = $title;
        $this->lastReminder = $lastReminder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
	    $pdf = PDF::loadView('pdf_order', ['order' => $this->order, 'orderDetails' => $this->orderDetails, 'typePdf' => 'Invoice']);
        return $this->markdown('Email.reminder_order')
        ->attachData($pdf->output(), 'Invoice #' . $this->order->order_id . '.pdf', [
            'mime' => 'application/pdf',
        ])
        ->subject($this->emailSubject)
        ->with([
            'title' => $this->title, 
            'sponsor' => $this->order->sponsorname, 
            'date' => Carbon::parse($this->order->created_at)->format('F d, Y'), 
            'titleOrder' => '[Donation #' . $this->order->order_id . '] (' .  Carbon::parse($this->order->created_at)->format('F d, Y') . ')', 
            'subtotal' => number_format($this->order->total_price, 2, ',', '.'),
            'total' => number_format($this->order->total_price, 2, ',', '.'),
            'payment_method' => $this->order->payment_type,
            'orderId' => $this->order->order_id,
            'orderDetails' => $this->orderDetails,
            'lastReminder' => $this->lastReminder
        ]);
    }
}
