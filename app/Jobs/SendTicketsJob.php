<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\TicketsMailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Например, класс для почтового уведомления

class SendTicketsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Order $order;

    /**
     * Создайте новый экземпляр задания.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Выполните задание.
     */
    public function handle(): void
    {
        $this->order->user->notify(new TicketsMailNotification($this->order));
    }
}
