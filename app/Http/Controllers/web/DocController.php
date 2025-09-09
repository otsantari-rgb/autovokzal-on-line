<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocRequest;
use App\Models\Doc;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Exception;

class DocController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $docs = Doc::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($doc) {
                return array_merge($doc->toArray(), [
                    'translated_gender' => $doc->getTranslatedGender(),
                    'translated_type' => $doc->getTranslatedType(),
                ]);
            });

        return Inertia::render('user/Docs', [
            'docs' => $docs,
        ]);
    }

    public function store(StoreDocRequest $request): RedirectResponse
    {
        Doc::firstOrCreate([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'patronymic' => $request->patronymic,
            'gender' => $request->gender,
            'type' => $request->type,
            'number' => $request->number,
            'birthday' => $request->birthday,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Документ успешно сохранён');
    }

    public function update(StoreDocRequest $request, Doc $doc)
    {
        if ($doc->user_id !== auth()->id()) {
            abort(403, 'У вас нет прав на изменение этого документа');
        }

        try {
            $doc->update($request->validated());

            return redirect()->back()->with('success', 'Документ успешно обновлён');
        } catch (Exception $e) {
            Log::error('Ошибка при обновлении документа: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'general' => 'Ошибка при обновлении документа. Попробуйте снова.',
            ]);
        }
    }

    public function destroy(Doc $doc)
    {
        if ($doc->user_id !== auth()->id()) {
            abort(403, 'У вас нет прав на удаление этого документа');
        }

        try {
            $doc->delete();

            return redirect()->back()->with('success', 'Документ успешно удалён');
        } catch (Exception $e) {
            Log::error('Ошибка при удалении документа: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'general' => 'Ошибка при удалении документа. Попробуйте снова.',
            ]);
        }
    }
}
