<?php

namespace App\Http\Controllers\web;

use App\Http\Requests\BookingDataRequest;
use App\Models\Doc;
use App\Models\Order;
use App\Models\Receipt;
use App\Services\AuthService;
use App\Services\BookingService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderController
{
    private BookingService $bookingService;

    private AuthService $authService;

    public function __construct(BookingService $bookingService, AuthService $authService)
    {
        $this->bookingService = $bookingService;
        $this->authService = $authService;
    }

    public function index(Request $request): Response
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->with('tickets')->get();

        $orders = $orders->map(function ($order) {
            return array_merge($order->toArray(), [
                'translated_status' => $order->getTranslatedStatus(),
            ]);
        });

        return Inertia::render('user/MyOrders', [
            'orders' => $orders,
        ]);
    }

    public function show(string $uuid): Response
    {
        $order = Order::with('tickets')->where('uuid', $uuid)->firstOrFail();
        $receipt = Receipt::where('external_id', $uuid . '-sell')->first();

        $orderData = [
            'id'                => $order->id,
            'uuid'              => $order->uuid,
            'status'            => $order->status,
            'translated_status' => $order->getTranslatedStatus(),
            'created_at'        => $order->created_at,
            'receipt'           => $receipt,
            'tickets'           => $order->tickets->map(function ($ticket) {
                return [
                    'id'                => $ticket->id,
                    'uuid'              => $ticket->uuid,
                    'status'            => $ticket->status,
                    'translated_status' => $ticket->getTranslatedStatus(),
                    'passenger_name'    => $ticket->user->name,
                    'route_name'        => $ticket->route_name,
                    'place'             => $ticket->place,
                    'departure_date'    => $ticket->departure_date,
                    'departure_time'    => $ticket->departure_time,
                    'arrival_date'      => $ticket->arrival_date,
                    'arrival_time'      => $ticket->arrival_time,
                ];
            }),
        ];

        return Inertia::render('orders/OrderDetails', [
            'order' => $orderData,
        ]);
    }

    public function getBookingData(BookingDataRequest $request): Response
    {
        $sheet = $request->sheet;
        $routeName = $sheet['name'];
        try {
            $bookingData = $this->bookingService->getData($sheet);
            $user = Auth::user();

            $docs = [];
            if ($user) {
                $docs = Doc::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($doc) {
                        return array_merge($doc->toArray(), [
                            'translated_gender' => $doc->getTranslatedGender(),
                            'translated_type' => $doc->getTranslatedType(),
                        ]);
                    });
            }

            return Inertia::render('orders/booking/Booking', [
                'title' => 'Бронирование',
                'success' => true,
                'bookingData' => $bookingData,
                'refundLimit' => config("refund_route_time_rules.$routeName"),
                'docs' => $docs,
                'user' => $user,
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), [$e->getTrace(), $e->getLine()]);
            Log::info(json_encode($request->get('sheet'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return Inertia::render('orders/booking/Booking', [
                'success' => false,
                'error' => 'Произошла ошибка',
            ]);
        }
    }

    public function confirmBooking(Request $request): RedirectResponse
    {
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

                // Передаём данные через flash в компонент
                return redirect()->back()->with('response', [
                    'status' => 'success',
                    'message' => 'Места успешно забронированы',
                    'paymentUrl' => $paymentUrl,
                ]);
            } else {
                // Не подтверждённый email или гость
                $email = Auth::check() ? Auth::user()->email : $request->contact_info['email'];
                $this->authService->sendVerificationCode($email);

                return redirect()->back()->with('response', [
                    'status' => 'need_verification',
                    'message' => 'Места забронированы, подтвердите электронную почту, чтобы перейти к оплате',
                    'email' => $email,
                ]);
            }
        } catch (Exception $e) {
            Log::error('Booking error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('response', [
                'status' => 'error',
                'message' => 'Не удалось забронировать билеты',
            ]);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyCode(Request $request)
    {
        Log::info(json_encode($request->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        try {
            $verified = $this->authService->verifyEmailCode($request->email, $request->code);

            if (! $verified) {
                return back()->with('response', [
                    'status' => 'error',
                    'message' => 'Неверный код',
                ]);
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

            return redirect()->back()->with('response', [
                'status' => 'success',
                'token' => $token,
                'user' => $user,
                'paymentUrl' => $paymentUrl,
            ]);
        } catch (Exception $e) {
            Log::error('Booking error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('response', [
                'status' => 'error',
                'message' => 'ошибка при бронировании',
            ]);
        }
    }
}
