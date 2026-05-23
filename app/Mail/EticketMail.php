<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EticketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        // Pastikan data event & tiket terbawa ke email
        $order->load('event', 'ticketType'); 
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('🎫 E-Tiket SPECTIX - ' . $this->order->event->name)
                    ->view('emails.ticket'); // Mengarah ke desain email yang tadi kita buat
    }
}