<?php

namespace App\Jobs;

use App\Mail\TicketRefundedToAdminMail;
use App\Mail\TicketRefundedToUserMail;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRefundMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Ticket $ticket;

    protected string $role;

    protected string $comment;
    protected float $refundAmount;

    /**
     * Create a new job instance.
     */
    public function __construct(Ticket $ticket, string $role, string $comment, float $refundAmount)
    {
        $this->ticket = $ticket;
        $this->role = $role;
        $this->comment = $comment;
        $this->refundAmount = $refundAmount;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->role !== 'admin') {
            Mail::to($this->ticket->user->email)
                ->send(new TicketRefundedToUserMail($this->ticket));
        }
        Mail::to(config('mail.admin_email'))
            ->send(new TicketRefundedToAdminMail($this->ticket, $this->role, $this->comment, $this->refundAmount));
    }
}
