<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\MoreRoutesController;
use App\Http\Controllers\api\DocController;
use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\ReceiptCallbackController;
use App\Http\Controllers\api\SearchController;
use App\Http\Controllers\api\TicketController;
use App\Http\Controllers\api\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::get('/stations', [HomeController::class, 'all'])->name('api.stations.list');
Route::get('/more-routes', [HomeController::class, 'moreRoutes'])->name('more.routes.list');
Route::get('/cities', [HomeController::class, 'cities'])->name('api.cities');
Route::get('/popular-routes', [HomeController::class, 'popularRoutes'])->name('api.popular.routes');
Route::get('/search', [SearchController::class, 'searchStations'])->name('search.stations');
Route::post('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('/orders/booking-data', [OrderController::class, 'getBookingData'])->name('api.booking.data');
Route::post('/orders/confirm-booking', [OrderController::class, 'confirmBooking'])->name('api.confirm.booking');
Route::post('/orders/verify-code', [OrderController::class, 'verifyCode'])->name('api.verify.code');
Route::match(['GET', 'POST'], '/payments/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/orders', [OrderController::class, 'index'])->middleware(['auth:sanctum'])->name('api.orders.index');
Route::get('/orders/{uuid}', [OrderController::class, 'show'])->middleware(['auth:sanctum'])->name('api.orders.show');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/email/verify/{id}/{hash}/', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json(['message' => 'Email успешно подтвержден!']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

//Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])->middleware(['auth:sanctum']);
Route::get('/user', [UserController::class, 'getUser'])->middleware(['auth:sanctum']);
Route::put('/user', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::get('/ticket/{uuid}', [TicketController::class, 'getTicket'])->middleware(['auth:sanctum'])->name('get.ticket');

Route::get('/more-routes', [\App\Http\Controllers\api\MoreRoutesController::class, 'index']);


Route::post('/ticket/refund', [TicketController::class, 'refund'])->middleware(['auth:sanctum', 'admin'])->name('ticket.refund');

Route::post('/ticket/refund-info', [TicketController::class, 'getRefundInfo'])->middleware(['auth:sanctum', 'admin'])
    ->name('ticket.refund.info');

Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.reset');

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/tickets', [TicketController::class, 'index']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/docs', [DocController::class, 'store']);
    Route::get('/docs', [DocController::class, 'index']);
    Route::put('/docs/{doc}', [DocController::class, 'update']);
    Route::delete('/docs/{doc}', [DocController::class, 'destroy']);
});
Route::get('/payment-status/{uuid}', [PaymentController::class, 'status']);

Route::match(['GET', 'POST'], '/receipt-callback', [ReceiptCallbackController::class, 'handleCallback']);
