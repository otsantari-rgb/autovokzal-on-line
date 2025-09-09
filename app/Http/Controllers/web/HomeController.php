<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\MoreRoute;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function show()
    {
        return Inertia::render('home/Home', [
            'title' => 'Автовокзал Улан-Удэ | Бронирование билетов онлайн',
            'description' => 'Купить автобусные билеты онлайн за 5 минут, без очередей.'
        ]);
    }
    public function showPolicyForm()
    {
        return Inertia::render('Policy', [
            'title' => 'Политика обработки персональных данных',
            'description' => 'Политика обработки персональных данных сервиса бронирования билетов "Автовокзал онлайн"'
        ]);
    }

    public function showOfertaForm()
    {
        return Inertia::render('Oferta', [
            'title' => '',
            'description' => 'Публичная оферта и условия предоставления услуг сервиса бронирования билетов "Автовокзал онлайн"'
        ]);
    }

    public function showPersonalDataForm()
    {
        return Inertia::render('PersonalData', [
            'title' => '',
            'description' => 'Политика обработки персональных данных пассажиров сервиса бронирования билетов "Автовокзал онлайн"'
        ]);
    }

    public function showMoreRoutesForm()
    {
        return Inertia::render('moreRoutes/MoreRoutes', [
            'title' => 'Популярные направления',
            'description' => 'Автобусные маршруты из Улан-Удэ: Иркутск, Чита, Кяхта, Закаменск, Турунтаево, Горячинск, Хоринск, Максимиха, Усть-Баргузин и ещё более 80 направлений',
            'routes' => MoreRoute::orderBy('start', 'asc')->get()
        ]);
    }

    public function showFaqForm()
    {
        return Inertia::render('Faq', [
            'title' => 'Часто задаваемые вопросы',
            'description' => 'Ответы на популярные вопросы о покупке билетов'
        ]);
    }

    public function showBaikalForm()
    {
        return Inertia::render('Baikal', [
            'title' => 'Автовокзал в Улан-Удэ "Байкал"',
            'description' => 'Автовокзал в Улан-Удэ "Байкал" информационная страница'
        ]);
    }
}


