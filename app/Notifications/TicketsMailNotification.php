<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketsMailNotification extends Notification
{
    public Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail']; // Выбираем канал отправки
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $user = $this->order->user()->first();
        $ticketUrl = route('tickets.pdf', ['uuid' => $this->order->tickets()->first()->uuid]);

        return (new MailMessage())
            ->subject('Ваши билеты')
            ->greeting('Здравствуйте, ' . $user->name . '!')
            ->line('Спасибо за покупку билетов. Ниже приведена информация о вашем заказе:')
            ->action('Скачать билеты', $ticketUrl)
            ->line('Если у вас есть вопросы, не стесняйтесь обращаться к нам.')
            ->salutation('С наилучшими пожеланиями, ' . config('app.name'));
    }
}
