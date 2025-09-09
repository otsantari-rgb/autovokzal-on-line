<?php

namespace App\Services;

use App\Jobs\SendVerificationCodeJob;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AuthService
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendVerificationCode($email): void
    {
        $existingCode = cache()->get("verification_code_{$email}");
        Log::info($existingCode);
        if (! $existingCode) {
            $code = rand(1000, 9999);
            cache()->put("verification_code_{$email}", $code, now()->addMinutes(10));
        } else {
            $code = $existingCode;
        }

        // Отправьте email с кодом
        SendVerificationCodeJob::dispatch($email, $code)->onQueue('verification-code');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyEmailCode($email, $code): bool
    {
        $savedCode = cache()->get("verification_code_{$email}");
        Log::info($email);
        Log::info($code);
        Log::info($savedCode);

        return $savedCode == $code;
    }

    public function findOrCreateUser($email): User
    {
        $user = User::where('email', $email)->first();
        if (! $user) {
            $password = Str::random(10);
            $user = User::create([
                'name' => $email,
                'email' => $email,
                'password' => bcrypt($password),
            ]);
            $user->markEmailAsVerified();
            // Отправьте email с паролем
            SendWelcomeEmailJob::dispatch($user, $password)->onQueue('welcome');
        } elseif (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return $user;
    }
}
