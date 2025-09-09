<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Jobs\SendVerificationEmailJob;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Response;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return Inertia::render('auth/Register', [ 'title' => 'Регистрация' ]);
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        SendVerificationEmailJob::dispatch($user)->onQueue('verification');
        Auth::login($user);

        return redirect()->route('verify-email')->with([
            'title' => 'Подтверждение почты',
            'toast' => [
                'message' => 'Пользователь успешно зарегистрирован. На ваш адрес электронной почты было отправлено письмо для подтверждения.',
                'type' => 'success'
            ],
            'auth' => ['user' => $user],
            'flash' => [
                'message' => 'Регистрация успешна! Письмо для подтверждения отправлено.',
                'type' => 'success'
            ]
        ]);
    }

    public function showLoginForm()
    {
        return Inertia::render('auth/Login', [
            'title' => "Вход",
        ]);
    }


    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended();
        }
        return back()->withErrors([
            'email' => 'Неверные данные для входа.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with([
            'toast' => [
                'message' => 'Вы успешно вышли из системы.',
                'type' => 'success'
            ],
            'auth' => ['user' => null],
        ]);
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! URL::hasValidSignature($request)) {
            return Inertia::render('auth/EmailVerification', [
                'title' => 'Ошибка подтверждения из-за недействительной ссылки',
                'status' => 'error',
                'errorMessage' => 'Недействительная ссылка подтверждения',
                'showResendButton' => true,
                'toast' => [
                    'message' => 'Недействительная ссылка подтверждения',
                    'type' => 'error'
                ]
            ]);
        }

        if (! hash_equals($hash, sha1($user->email))) {
            return Inertia::render('auth/EmailVerification', [
                'title' => 'Ошибка подтверждения. Недействительная ссылка',
                'status' => 'error',
                'errorMessage' => 'Недействительная ссылка подтверждения',
                'showResendButton' => true,
                'toast' => [
                    'message' => 'Недействительная ссылка подтверждения',
                    'type' => 'error'
                ]
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return Inertia::render('auth/EmailVerification', [
                'title' => 'Почта уже подтверждена',
                'status' => 'already_verified',
                'toast' => [
                    'message' => 'Ваш email уже был подтвержден ранее.',
                    'type' => 'info'
                ]
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            return Inertia::render('auth/EmailVerification', [
                'title' => 'Почта подтверждена',
                'status' => 'verified',
                'toast' => [
                    'message' => 'Ваш email успешно подтвержден!',
                    'type' => 'success'
                ]
            ]);
        }

        return Inertia::render('auth/EmailVerification', [
            'title' => 'Ошибка подтверждения',
            'status' => 'error',
            'errorMessage' => 'Ошибка при подтверждении email',
            'showResendButton' => true,
            'toast' => [
                'message' => 'Ошибка при подтверждении email',
                'type' => 'error'
            ]
        ]);
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect('/');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect('/');
        }

        return Inertia::render('auth/VerifyEmail', [
            'title' => "Подтверждение почты",
//            'message' => $request->session()->get('message', 'На ваш email было отправлено письмо для подтверждения.'),
//            'class' => $request->session()->get('class', 'alert-info'),
            'auth' => ['user' => $user],
        ]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Почта уже подтверждена'], 400);
            }

            return Inertia::render('auth/EmailVerification', [
                'title' => 'Почта подтверждена',
                'status' => 'already_verified',
                'toast' => [
                    'message' => 'Почта уже подтверждена',
                    'type' => 'info'
                ]
            ]);
        }

        SendVerificationEmailJob::dispatch($user)->onQueue('verification');


        return Inertia::render('auth/ResendVerifyEmail', [
            'title' => 'Повторное подтверждение почты',
            'toast' => [
                'message' => 'Письмо с подтверждением отправлено повторно. Проверьте свою почту.',
                'type' => 'success'
            ]
        ]);
    }


    public function showResetLinkEmailForm(): Response
    {
        return Inertia::render('auth/PasswordResetRequest', [ 'title' => 'Восстановление пароля' ]);
    }

    public function showResetPasswordForm(Request $request, $token): Response
    {
        return Inertia::render('auth/PasswordReset', [
            'title' => 'Сброс пароля',
            'token' => $token,
            'email' => $request->email,
            'errors' => session()->get('errors') ? session()->get('errors')->getBag('default')->getMessages() : [],
        ]);
    }


    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('showLoginForm')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
