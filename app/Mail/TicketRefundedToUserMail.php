<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketRefundedToUserMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build(): TicketRefundedToUserMail
    {
        return $this->subject('Заявка на возврат (autovokzal-on-line.ru) билета №' . $this->ticket->id . " из заказа №" . $this->ticket->order->id)
            ->view('emails.ticket_refunded_to_user')
            ->with([
                'ticket' => $this->ticket,
            ]);
    }
}
