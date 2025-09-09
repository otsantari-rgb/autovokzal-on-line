<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Jobs\SendVerificationEmailJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Метод для получения данных о текущем пользователе
    public function getUser(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();

        // Проверка изменения email
        if ($request->email !== $user->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
            $user->save();

            // Отправка письма для подтверждения нового email
            SendVerificationEmailJob::dispatch($user)->onQueue('verification');

            return response()->json([
                'message' => 'Профиль обновлен, но требуется подтвердить новый email.',
                'email_changed' => true,
            ]);
        }

        // Если пароль изменяется, проверяем текущий пароль
        if ($request->filled('new_password')) {
            // Проверка текущего пароля
            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Текущий пароль неверный.',
                ], 422);
            }

            // Обновление пароля
            $user->password = Hash::make($request->new_password);
        }

        // Обновление имени
        $user->update($request->only(['name']));

        return response()->json([
            'message' => 'Профиль успешно обновлен.',
            'email_changed' => false,
        ]);
    }
}
