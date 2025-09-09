<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\StoreDocRequest;
use App\Models\Doc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class DocController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $docs = Doc::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $docs = $docs->map(function ($doc) {
            return array_merge($doc->toArray(), [
                'translated_gender' => $doc->getTranslatedGender(),
                'translated_type' => $doc->getTranslatedType(),
            ]);
        });

        return response()->json($docs);
    }

    public function store(StoreDocRequest $request): JsonResponse
    {
        $doc = Doc::firstOrCreate([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'patronymic' => $request->patronymic,
            'gender' => $request->gender,
            'type' => $request->type,
            'number' => $request->number,
            'birthday' => $request->birthday,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Документ успешно сохранён',
            'doc' => $doc,
        ], 201);
    }

    public function update(StoreDocRequest $request, Doc $doc): JsonResponse
    {
        if ($doc->user_id !== auth()->id()) {
            Log::warning("Попытка обновления документа другим пользователем: Документ ID {$doc->id}, Пользователь ID " . auth()->id());

            return response()->json(['message' => 'У вас нет прав на изменение этого документа'], 403);
        }

        try {
            // Обновляем документ с помощью валидации
            $doc->update($request->validated());

            return response()->json([
                'message' => 'Документ успешно обновлён',
                'doc' => $doc,
            ]);
        } catch (Exception $e) {
            Log::error('Ошибка при обновлении документа: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ошибка при обновлении документа. Попробуйте снова.',
            ], 500);
        }
    }

    public function destroy(Doc $doc): JsonResponse
    {
        if ($doc->user_id !== auth()->id()) {
            return response()->json(['message' => 'У вас нет прав на удаление этого документа'], 403);
        }

        try {
            $doc->delete();

            return response()->json(['message' => 'Документ успешно удалён']);
        } catch (Exception $e) {
            Log::error('Ошибка при удалении документа: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ошибка при удалении документа. Попробуйте снова.',
            ], 500);
        }
    }
}
