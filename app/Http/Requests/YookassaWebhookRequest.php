<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class YookassaWebhookRequest extends FormRequest
{
    /**
     * Разрешение на выполнение запроса.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->isValidIp();
    }

    /**
     * Правила валидации запроса.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'event' => 'required|string', // Пример проверки события
        ];
    }

    /**
     * Проверяет, что запрос пришёл с разрешённого IP-адреса.
     *
     * @return bool
     */
    private function isValidIp(): bool
    {
        $allowedIps = [
            '185.71.76.0/27',
            '185.71.77.0/27',
            '77.75.153.0/25',
            '77.75.156.11',
            '77.75.156.35',
            '77.75.154.128/25',
            '2a02:5180::/32'
        ];

        $clientIp = $this->header('X-Forwarded-For') ?: $this->ip();

        foreach ($allowedIps as $allowedIp) {
            if ($this->ipInRange($clientIp, $allowedIp)) {
                return true;
            }
        }

        Log::warning('Invalid IP address for webhook', ['client_ip' => $clientIp]);
        return false;
    }

    /**
     * Проверяет, если IP в пределах диапазона.
     *
     * @param string $ip
     * @param string $range
     * @return bool
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (!str_contains($range, '/')) {
            return $ip === $range;
        }

        list($subnet, $bits) = explode('/', $range);
        $subnet = ip2long($subnet);
        $ip = ip2long($ip);
        $mask = -1 << (32 - $bits);
        return ($ip & $mask) === ($subnet & $mask);
    }
}
