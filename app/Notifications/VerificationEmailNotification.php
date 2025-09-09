<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class VerificationEmailNotification extends Notification
{
    use Queueable;

    public function __construct() {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);
        Log::info($url);

        return (new MailMessage())
            ->subject('Уведомление от ' . config('app.name'))
            ->greeting('Здравствуйте!')
            ->line('Спасибо за использование нашего сайта!')
            ->line('Пожалуйста, выполните действие ниже.')
            ->action('Подтвердить адрес электронной почты', $url)
            ->line('Если вы не создавали учетную запись, просто игнорируйте это письмо.')
            ->salutation('С уважением, ' . config('app.name'));
    }

    protected function verificationUrl($notifiable): string
    {
        // тут оказывается важно чтобы FRONTEND_URL и APP_URL были одинаковыми значит переменная FRONTEND_URL нам не нужна
        $expires = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));
        // Создаём временно подписанную ссылку
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            $expires,
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Парсим ссылку и извлекаем необходимые данные
        $urlComponents = parse_url($verificationUrl);

        // Извлекаем параметры из запроса (id, hash, signature)
        parse_str($urlComponents['query'], $queryParams);

        // Формируем ссылку с параметрами
        return env('APP_URL') . "/email/verify/{$notifiable->getKey()}/" . sha1($notifiable->getEmailForVerification()) . '?' . http_build_query([
            'expires' => $expires->timestamp,
            'signature' => $queryParams['signature'],
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }
}
