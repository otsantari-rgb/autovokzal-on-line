<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class PasswordResetNotification extends Notification
{
    use Queueable;

    protected string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    // Определяем каналы доставки уведомления
    public function via($notifiable): array
    {
        return ['mail'];
    }

    // Подготавливаем представление уведомления для почты
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage())
            ->subject('Сброс пароля для ' . config('app.name'))
            ->greeting('Здравствуйте!')
            ->line('Вы получили это письмо, потому что мы получили запрос на сброс пароля для вашей учетной записи.')
            ->action('Сбросить пароль', $url)
            ->line('Если вы не запрашивали сброс пароля, просто проигнорируйте это сообщение.')
            ->salutation('С уважением, ' . config('app.name'));
    }

    // Генерация URL для сброса пароля
    protected function resetUrl($notifiable): string
    {
        // Формируем временно подписанную ссылку
        $passwordResetUrl = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(60),
            [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]
        );

        // Парсим ссылку для получения параметров
        $urlComponents = parse_url($passwordResetUrl);

        // Извлекаем параметры из запроса
        parse_str($urlComponents['query'], $queryParams);

        // Формируем финальную ссылку в требуемом формате
        return env('APP_URL') . "/password/reset/{$this->token}?email=" . urlencode($notifiable->getEmailForPasswordReset()) . '&expires=' . now()->addMinutes(60)->timestamp . '&signature=' . $queryParams['signature'];
    }

    // Поддержка массивного представления уведомления (необязательно)
    public function toArray($notifiable): array
    {
        return [

        ];
    }
}
