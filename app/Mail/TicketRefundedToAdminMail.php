<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketRefundedToAdminMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public Ticket $ticket;

    public string $role;

    protected string $comment;
    protected int|float $refundAmount;

    public function __construct(Ticket $ticket, string $role, string $comment,  int|float $refundAmount)
    {
        $this->ticket = $ticket;
        $this->role = $role;
        $this->comment = $comment;
        $this->refundAmount = $refundAmount;
    }

    public function build(): TicketRefundedToAdminMail
    {
        if ($this->role !== 'admin') {
            $userInfo = ' (проведен пользователем сайта)';
        } else {
            $userInfo = ' (проведен администратором)';
        }

        return $this->subject(
            'Заявка на возврат (autovokzal-on-line.ru) билета №'
            . $this->ticket->id . " из заказа №" . $this->ticket->order->id . $userInfo
        )
            ->view('emails.ticket_refunded_to_admin')
            ->with([
                'ticket' => $this->ticket,
                'comment' => $this->comment,
                'refundAmount' => $this->refundAmount,
            ]);
    }
}
