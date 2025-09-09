<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRefundedTicketToAdminMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Ticket $ticket;
    protected string $comment;
    protected int|float $refundAmount;
    protected string $username;


    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket, string $comment, int|float $refundAmount = null , string $username = null)
    {
        $this->ticket = $ticket;
        $this->comment = $comment;
        $this->refundAmount = $refundAmount;
        $this->username = $username;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userId = $this->ticket->user_id;
        $user = User::where('id', $userId)->first();
        $admin = empty($this->username) ? 'администратор' : $this->username;
        $subject = 'Провел(а) возврат (autovokzal-on-line.ru) билета №' . $this->ticket->id . " из заказа №" . $this->ticket->order->id ." (" . $admin . ")";

        Mail::send('emails.refunded_ticket', [
            'ticket' => $this->ticket,
            'comment' => $this->comment,
            'refundAmount' => $this->refundAmount,
            'username' => $this->username,
            'phone' => $this->ticket->passenger_phone,
            'email' => $user->email,
            'name' => $user->name,
        ], function ($message) use ($subject) {
            $message->to(config('mail.admin_email'))
                ->subject($subject);
        });
    }
}
