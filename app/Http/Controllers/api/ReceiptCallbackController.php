<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Receipt;

class ReceiptCallbackController extends Controller
{
    public function handleCallback(Request $request): void
    {
        // Логируем входящий запрос
        Log::info('Пришел callback:', $request->all());

        // Проверяем, есть ли нужные данные
        if (!$request->has(['uuid', 'status', 'external_id', 'payload'])) {
            Log::error('Ошибка: Неполные данные в callback', $request->all());
        }

        // Проверяем статус чека
        if ($request->status === 'done') {
            Log::info('Чек успешно зарегистрирован: ' . $request->external_id);

            // Сохраняем или обновляем информацию в БД через Eloquent
            Receipt::updateOrCreate(
                ['external_id' => $request->external_id], // Критерий поиска
                [
                    'uuid' => $request->uuid,
                    'status' => $request->status,
                    'total' => $request->payload['total'] ?? null,
                    'fiscal_receipt_number' => $request->payload['fiscal_receipt_number'] ?? null,
                    'shift_number' => $request->payload['shift_number'] ?? null,
                    'receipt_datetime' => $request->payload['receipt_datetime'] ?? null,
                    'fn_number' => $request->payload['fn_number'] ?? null,
                    'ecr_registration_number' => $request->payload['ecr_registration_number'] ?? null,
                    'fiscal_document_number' => $request->payload['fiscal_document_number'] ?? null,
                    'fiscal_document_attribute' => $request->payload['fiscal_document_attribute'] ?? null,
                    'fns_site' => $request->payload['fns_site'] ?? null,
                    'ofd_inn' => $request->payload['ofd_inn'] ?? null,
                    'ofd_receipt_url' => $request->payload['ofd_receipt_url'] ?? null,
                ]
            );
        } else {
            Log::error('Ошибка в чеке: ' . json_encode($request->error, JSON_UNESCAPED_UNICODE));
        }
    }
}
