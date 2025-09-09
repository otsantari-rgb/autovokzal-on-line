<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNewUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Добро пожаловать!')
            ->line('Добро пожаловать в на наш сайт!')
            ->line('Ваша учетная запись была успешно создана.')
            ->line('Ваш временный пароль: ' . $this->password)
            ->line('Мы настоятельно рекомендуем вам изменить этот пароль после первого входа в систему.')
            ->action('Войти в приложение', url('/login'))
            ->line('Если у вас возникнут какие-либо вопросы, пожалуйста, не стесняйтесь обращаться к нам.');
    }
}
