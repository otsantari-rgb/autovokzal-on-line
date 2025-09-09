<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\RefundRequest;
use App\Models\Ticket;
use App\Services\RefundService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TicketController
{
    private RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    public function index(Request $request): JsonResponse
    {
        // Извлекаем параметры фильтрации из запроса
        $purchaseStartDate = $request->query('purchase_start_date');
        $purchaseEndDate = $request->query('purchase_end_date');
        $departureStartDate = $request->query('departure_start_date');
        $departureEndDate = $request->query('departure_end_date');

        // Строим запрос к базе данных
        $query = Ticket::with(['order', 'user']);

        // Применяем фильтрацию по дате покупки
        if ($purchaseStartDate && $purchaseEndDate) {
            $purchaseEndDate = Carbon::createFromFormat('Y-m-d', $purchaseEndDate)->endOfDay();
            $query->whereBetween('created_at', [$purchaseStartDate, $purchaseEndDate]);
        }

        // Применяем фильтрацию по дате отправления
        if ($departureStartDate && $departureEndDate) {
            $query->whereBetween('departure_date', [$departureStartDate, $departureEndDate]);
        }

        // Получаем результаты
        $tickets = $query->orderBy('id')->get();

        // Формируем результат, добавляя дополнительные поля
        $tickets = $tickets->map(function ($ticket) {
            return array_merge($ticket->toArray(), [
                'order' => $ticket->order->id,
                'ba_operation_id' => $ticket->order->ba_operation_id,
                'email' => $ticket->user->email,
                'translated_status' => $ticket->getTranslatedStatus(),
            ]);
        });

        // Возвращаем ответ
        return response()->json(['tickets' => $tickets]);
    }

    public function getTicket($uuid): JsonResponse
    {
        try {
            $ticket = Ticket::where('uuid', $uuid)->firstOrFail();

            return response()->json([
                'ticket' => $ticket,
                'status' => $ticket->getTranslatedStatus(),
                'refund_limit' => config("refund_route_time_rules.$ticket->route_name"),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Билет не найден'], 404);
        }
    }

    public function refund(RefundRequest $request): JsonResponse
    {
        try {
            $ticket = Ticket::where('ba_ticket_id', $request->id)->first();
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Билет с таким id не найден'
                ], 422);
            }

            $refundAmount = $request->refund_amount ?? 0;
            $comment = $request->comment ?? '';
            $username = $request->username ?? '';

            $price = $ticket->price;

            if ( $ticket->status === 'refunded' ){
                return response()->json([
                    'success' => true,
                    'message' => 'Билет уже возвращён'
                ]);
            } elseif ($ticket->status === "canceled"){
                return response()->json([
                    'success' => true,
                    'message' => 'Билет уже отменен, возврат невозможен'
                ]);
            } else{
                if ($refundAmount > $price){
                    return response()->json([
                        'success' => false,
                        'message' => 'Сумма возврата не должна превышать стоимость билета'
                    ]);
                }
                $response = $this->refundService->refundAdmin($ticket, $refundAmount, $comment, $username);
                return response()->json($response);
            }
        } catch (Exception $e) {
            Log::info($e);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при оформлении возврата!',
            ]);
        }
    }

    public function getRefundInfo(Request $request): JsonResponse
    {
        $ticket = Ticket::where('uuid', $request->get('uuid'))->firstOrFail();

        $maxRefundAmount = $ticket->price;
        $calculatedRefundAmount = $this->refundService->calculateRefundAmount($ticket);

        return response()->json([
            'max_refund_amount' => $maxRefundAmount,
            'calculated_refund_amount' => $calculatedRefundAmount,
        ]);
    }
}
