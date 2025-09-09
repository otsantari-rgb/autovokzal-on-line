<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingDataRequest;
use App\Models\Order;
use App\Models\Receipt;
use App\Services\AuthService;
use App\Services\BookingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderController extends Controller
{
    private BookingService $bookingService;

    private AuthService $authService;

    public function __construct(BookingService $bookingService, AuthService $authService)
    {
        $this->bookingService = $bookingService;
        $this->authService = $authService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        Log::info($user);
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->with('tickets')->get();

        $orders = $orders->map(function ($order) {
            return array_merge($order->toArray(), [
                'translated_status' => $order->getTranslatedStatus(),
            ]);
        });

        return response()->json($orders);
    }

    public function show(string $uuid): JsonResponse
    {
        $order = Order::with('tickets')->where('uuid', $uuid)->firstOrFail();
        $receipt = Receipt::where('external_id', $uuid . '-sell')->first() ?? null;

        // Форматирование данных, если нужно, например, добавляем перевод статуса
        $orderData = [
            'id' => $order->id,
            'uuid' => $order->uuid,
            'status' => $order->status,
            'translated_status' => $order->getTranslatedStatus(),
            'created_at' => $order->created_at,
            'receipt' => $receipt,
            'tickets' => $order->tickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'uuid' => $ticket->uuid,
                    'status' => $ticket->status,
                    'translated_status' => $ticket->getTranslatedStatus(),
                    'passenger_name' => $ticket->user->name,
                    'route_name' => $ticket->route_name,
                    'place' => $ticket->place,
                    'departure_date' => $ticket->departure_date,
                    'departure_time' => $ticket->departure_time,
                    'arrival_date' => $ticket->arrival_date,
                    'arrival_time' => $ticket->arrival_time,
                ];
            }),
        ];

        return response()->json($orderData);
    }

    public function getBookingData(BookingDataRequest $request): JsonResponse
    {
        $sheet = $request->sheet;
        $routeName = $sheet['name'];
        try {
            $bookingData = $this->bookingService->getData($sheet);

            return response()->json([
                'success' => true,
                'data' => $bookingData,
                'refund_limit' => config("refund_route_time_rules.$routeName"),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::info(json_encode($request->get('sheet'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return response()->json(['success' => false, 'error' => 'Произошла ошибка'], 500);
        }
    }

    public function confirmBooking(Request $request): JsonResponse
    {
        if ($request->bearerToken()) {
            Auth::setUser(PersonalAccessToken::findToken($request->bearerToken())?->tokenable);
        }
        Log::info('Confirm booking called', [
            'request' => $request->all(),
            'user' => Auth::user(),
            'time' => now()->toDateTimeString(),
        ]);
        try {
            if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
                $user = Auth::user();
                $paymentUrl = $this->bookingService->processBooking(
                    $request->sheet_id,
                    $request->price_id,
                    $request->contact_info['phone'],
                    $request->passenger_data,
                    $request->total_price,
                    $user,
                    $request->payment_method
                );
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json(['status' => 'success', 'token' => $token, 'paymentUrl' => $paymentUrl]);
            } else {
                // Если пользователь авторизован, но email не подтвержден, или пользователь не авторизован
                $email = Auth::check() ? Auth::user()->email : $request->contact_info['email'];
                $this->authService->sendVerificationCode($email);

                return response()->json(['status' => 'need_verification', 'email' => $email]);
            }
        } catch (Exception $e) {
            // Обработка всех исключений
            Log::error('Booking error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Не удалось забронировать билеты'], 500);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyCode(Request $request): JsonResponse
    {
        Log::info(json_encode($request->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        try {
            $verified = $this->authService->verifyEmailCode($request->email, $request->code);

            if (! $verified) {
                return response()->json(['status' => 'error', 'message' => 'Неверный код'], 400);
            }

            $user = $this->authService->findOrCreateUser($request->email);
            $token = $user->createToken('auth_token')->plainTextToken;
            Auth::login($user);
            $paymentUrl = $this->bookingService->processBooking(
                $request->sheet_id,
                $request->price_id,
                $request->contact_info['phone'],
                $request->passenger_data,
                $request->total_price,
                $user,
                $request->payment_method
            );

            return response()->json(['status' => 'success', 'token' => $token, 'user' => $user, 'payment_url' => $paymentUrl]);
        } catch (Exception $e) {
            Log::error('Booking error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'ошибка при бронировании']);
        }
    }
}
