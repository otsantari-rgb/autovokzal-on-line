<?php

namespace App\Services;

use App\Clients\BiletAvtoApiClient;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use YooKassa\Common\Exceptions\ApiConnectionException;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\AuthorizeException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;

class BookingService
{
    protected BiletAvtoApiClient $client;

    private PaymentService $paymentService;

    public function __construct(BiletAvtoApiClient $client, PaymentService $paymentService)
    {
        $this->client = $client;
        $this->paymentService = $paymentService;
    }

    /**
     * @throws Exception
     */
    public function getData($requestData): array
    {
        $sheetId = $requestData['id'];
        $priceId = $requestData['price_id'];

        try {
            $result = $this->client->getRide($sheetId, $priceId)['data'][0];

            // Пример обработки полученных билетов
            $bookedSeats = $result['places']['occupied'];
            $countPlaces = $result['places']['countPlaces'];
            $seatMap = $this->generateSeatMap($countPlaces);

            return [
                'sheet' => $result,
                'countPlaces' => $countPlaces,
                'bookedSeats' => $bookedSeats,
                'seatMap' => $seatMap,
                'to' => $result['arrivalCity'],
                'data' => $requestData,
                'priceId' => $priceId,
            ];
        } catch (ConnectionException $e) {
            Log::error($e->getMessage() . ' on line ' . $e->getLine());
            throw new Exception('Произошла ошибка при получении данных.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception('Произошла непредвиденная ошибка.');
        }
    }

    /**
     * Обрабатывает информацию о пассажирах.
     *
     * @throws Exception
     */
    public function processPassengers(array $passengers): array
    {
        return array_map(function ($passenger) {
            // Обработка пола пассажира
            if ($passenger['gender'] === 'male') {
                $genderId = 1;
            } elseif ($passenger['gender'] === 'female') {
                $genderId = 0;
            } else {
                Log::error('Ошибка пола для пассажира: ' . json_encode($passenger));
                throw new Exception('Ошибка пола пассажира: ' . $passenger['lastname'] . ' ' . $passenger['firstname']);
            }

            // Формирование данных пассажира
            return [
                'place' => $passenger['place'],
                'passportNumber' => $passenger['docs_number'],
                'name' => $passenger['lastname'] . ' ' . $passenger['firstname'] . ' ' . ($passenger['patronymic'] ?? ' '),
                'gender_id' => $genderId,
                'birthday' => $passenger['birthday'],
            ];
        }, $passengers);
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function bookTickets($rideId, $priceId, $phone, $passengers, $totalPrice, $user, $paymentMethod): array
    {
        // Обрабатываем пассажиров
        $processedPassengers = $this->processPassengers($passengers);

        DB::beginTransaction();
        try {
            // Запрос к API для бронирования билетов
            $result = $this->client->book($rideId, $priceId, $user->email, $phone, $processedPassengers);
            Log::info('Booking result: ' . json_encode($result));
            if (! isset($result['data']['tickets'])) {
                Log::error('Не удалось забронировать билеты.', ['result' => $result]);
                throw new Exception('Не удалось забронировать билеты');
            }

            $order = $this->createOrder($user, $totalPrice, $result['data']['operationId']);

            // Создание билетов
            $this->createTickets($order, $result['data']['tickets'], $priceId, $user, $passengers);

            // Создание платежа
            $payment = $this->createPayment($order, $totalPrice, $paymentMethod);
            DB::commit();

            return ['order' => $order, 'payment' => $payment];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Booking error: ' . $e->getMessage(), ['line' => $e->getLine(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    protected function createOrder($user, $totalPrice, $operationId)
    {
        return Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'ba_operation_id' => $operationId,
        ]);
    }

    protected function createTickets($order, $tickets, $priceId, $user, $passengers): void
    {
        foreach ($tickets as $index => $ticket) {
            Ticket::create([
                'ride_id' => $ticket['rideId'],
                'price_id' => $priceId,
                'ba_ticket_id' => $ticket['ticketId'],
                'user_id' => $user->id,
                'passenger_phone' => $ticket['phone'],
                'passenger_name' => $passengers[$index]['lastname'] . ' ' . $passengers[$index]['firstname'] . ' ' . ($passengers[$index]['patronymic'] ?? ' '),
                'passenger_gender' => $passengers[$index]['gender'],
                'passenger_docs_type' => $passengers[$index]['document_type'],
                'passenger_docs_number' => $passengers[$index]['docs_number'],
                'place' => $ticket['place'],
                'nominal' => $ticket['nominal'],
                'price' => $ticket['price'],
                'departure_station' => $ticket['departureCity'],
                'departure_date' => $ticket['departureDate'],
                'departure_time' => $ticket['departureTime'],
                'departure_address' => $ticket['departureStation'],
                'arrival_station' => $ticket['arrivalCity'],
                'arrival_date' => $ticket['arrivalDate'],
                'arrival_time' => $ticket['arrivalTime'],
                'arrival_address' => $ticket['arrivalStation'],
                'route_name' => $ticket['routeName'],
                'type' => 'Полный',
                'order_id' => $order->id,
                'status' => 'booked',
            ]);
        }
    }

    protected function createPayment($order, $totalPrice, $paymentMethod)
    {
        return Payment::create([
            'order_id' => $order->id,
            'amount' => $totalPrice,
            'method' => $paymentMethod,
            'status' => 'pending',
        ]);
    }

    private function generateSeatMap(int $totalSeats): array
    {
        $arr_place = [];
        $number_place = 1;
        $columns = 5; // Количество колонок в ряду
        $count_rows = ceil($totalSeats / ($columns - 1)); // Общее количество рядов

        // Заполняем места
        for ($i = 0; $i < $count_rows; $i++) {
            for ($j = 0; $j < $columns; $j++) {
                if ($j == 2) {
                    $arr_place[$i][$j] = 0;
                } else {
                    $arr_place[$i][$j] = $number_place <= $totalSeats ? $number_place : null;
                    $number_place++;
                }
            }
        }

        return $arr_place;
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws AuthorizeException
     * @throws InternalServerError
     * @throws ConnectionException
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ApiConnectionException
     * @throws UnauthorizedException
     * @throws Exception
     */
    public function processBooking($sheetId, $priceId, $phone, $passengers, $totalPrice, $user, $paymentMethod): string
    {
        try {
            $result = $this->bookTickets($sheetId, $priceId, $phone, $passengers, $totalPrice, $user, $paymentMethod);

            return $this->paymentService->createPayment($result['order'], $result['payment']);
        } catch (Exception $e) {
            Log::error('Booking process failed: ' . $e->getMessage(), ['line' => $e->getLine(), 'trace' => $e->getTraceAsString()]);
            throw new Exception('Ошибка при бронировании билетов: ' . $e->getMessage());
        }
    }
}
