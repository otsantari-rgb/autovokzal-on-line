<?php

use App\Http\Controllers\web\AuthController;
use App\Http\Controllers\web\DocController;
use App\Http\Controllers\web\HomeController;
use App\Http\Controllers\web\OrderController;
use App\Http\Controllers\web\SearchController;
use App\Http\Controllers\web\TicketController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\web\StationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/{direction}', [SearchController::class, 'directional'])
    ->where('direction', '[a-z-]+--[a-z-]+')
    ->name('search.directional');
Route::get('/booking', [OrderController::class, 'getBookingData'])->name('booking.data');
Route::post('/orders/confirm-booking', [OrderController::class, 'confirmBooking']);
Route::post('/orders/verify-code', [OrderController::class, 'verifyCode']);


Route::get('/tickets/pdf/{uuid}', [TicketController::class, 'generatePdf'])->name('tickets.pdf');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('showLoginForm');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'] )->name('showRegisterForm');
Route::post('/register', [AuthController::class, 'register']);



Route::get('/password/reset', [AuthController::class, 'showResetLinkEmailForm'])->name('password.request');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('showResetPasswordForm');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/more-routes', [HomeController::class, 'showMoreRoutesForm'])->name('more-routes');
Route::get('/policy', [HomeController::class, 'showPolicyForm'])->name('policy');
Route::get('/personal-data', [HomeController::class, 'showPersonalDataForm'])->name('personal-data');
Route::get('/oferta', [HomeController::class, 'showOfertaForm'])->name('oferta');
Route::get('/faq', [HomeController::class, 'showFaqForm'])->name('faq');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');


Route::get('/email/verify-email', [AuthController::class, 'sendVerificationEmail'])->name('verify-email');
Route::get('/email/resend-verify-email', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['auth'])->name('resend');

Route::middleware(['auth'])->prefix('account')->group(function () {
    Route::get('/', fn () => Inertia::render('user/Account'))->name('account');

    Route::get('/profile', fn () => Inertia::render('user/Profile', [
        'user' => Auth::user(),
        'title' => 'Профиль пользователя',
    ]))->name('account.profile');
    Route::get('/orders', [OrderController::class, 'index'])->name('account.orders');
    Route::get('/orders/{uuid}', [OrderController::class, 'show'])->name('account.order');
    Route::get('/docs', [DocController::class, 'index'])->name('docs.index');
    Route::post('/docs', [DocController::class, 'store'])->name('docs.store');
    Route::put('/docs/{doc}', [DocController::class, 'update'])->name('docs.update');
    Route::delete('/docs/{doc}', [DocController::class, 'destroy'])->name('docs.destroy');
    Route::get('/edit', fn () => Inertia::render('user/Edit'))->name('account.edit');
});

Route::get('/refund/{uuid}', [TicketController::class, 'showRefundTicket'])->middleware(['auth'])->name('refund.ticket.show');
Route::post('/ticket/refund', [TicketController::class, 'refund'])->middleware(['auth'])->name('refund.ticket');
Route::fallback(function () {
    if (request()->is('api/*')) {
        return response()->json(['message' => 'Not Found'], 404);
    }
    return Inertia::render('home/NotFound', ['title' => 'Неизвестная страница'])->toResponse(request())->setStatusCode(404);
});

Route::get('/payment-info/{uuid}', function ($uuid) {
    return Inertia::render('orders/PaymentInfo', [
        'uuid' => $uuid,
    ]);
})->name('payment.info');

Route::get('/contacts', function () {
    return Inertia::render('Contacts', [
        'description' => 'Контактная информация сервиса бронирования билетов "Автовокзал онлайн"'
    ]);
});

Route::get('/stations/ulan-ude-baikal', function () {
    return Inertia::render('stations/Baikal', [
        'description' => 'Автовокзал в Улан-Удэ «Байкал». Расписание автобусов автовокзала «Байкал» в Улан-Удэ'
    ]
);
})->name('stations.baikal');

Route::get('/stations', [StationController::class, 'index']);
Route::get('/stations/{stationKey}', [StationController::class, 'show']);