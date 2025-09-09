<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Jobs\SendPasswordResetEmailJob;
use App\Jobs\SendVerificationEmailJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->validated())) {
            throw ValidationException::withMessages(['email' => 'Неверный email или пароль']);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        Auth::login($user);

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // Удаление текущего токена
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Вы успешно вышли из системы.']);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        // Создание пользователя
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        SendVerificationEmailJob::dispatch($user)->onQueue('verification');
        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Возврат успешного ответа
        return response()->json([
            'message' => 'Пользователь успешно зарегистрирован. На ваш адрес электронной почты было отправлено письмо для подтверждения.',
            'redirect' => '/verify-email',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Почта уже подтверждена'], 400);
        }

        SendVerificationEmailJob::dispatch($user)->onQueue('verification');

        return response()->json(['message' => 'Письмо с подтверждением отправлено повторно'], 200);
    }

    public function sendResetLinkEmail(EmailRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return response()->json([
                'message' => 'Пользователь с таким адресом электронной почты не найден.',
            ], 404);
        }

        // Отправка задания в очередь
        SendPasswordResetEmailJob::dispatch($user)->onQueue('password-reset');

        return response()->json([
            'message' => 'Письмо с восстановлением пароля было успешно отправлено!',
        ]);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        Log::info(json_encode($request->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);  // Хеширование нового пароля
                $user->save();  // Сохранение пользователя
            }
        );
        Log::info($response);
        // Проверка, был ли успешен сброс пароля
        if ($response == Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Ваш пароль был успешно изменен!',
                'redirect' => route('login'),  // Возвращаем URL для редиректа
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось изменить пароль!',
        ], 422);
    }
}
