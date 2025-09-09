<?php

namespace App\Jobs;

use App\Notifications\VerificationCodeNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendVerificationCodeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $email;

    protected int $code;

    public function __construct($email, $code)
    {
        $this->email = $email;
        $this->code = $code;
    }

    public function handle(): void
    {
        Notification::route('mail', $this->email)
            ->notify(new VerificationCodeNotification($this->code));
    }
}
