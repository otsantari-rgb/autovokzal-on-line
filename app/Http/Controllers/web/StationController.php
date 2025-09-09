<?php

namespace App\Http\Controllers\web;

use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StationController extends Controller
{
    /**
     * Показывает список всех станций
     */
    public function index(): Response
    {
        $stations = Config::get('stations.autovokzal.stations');

        return Inertia::render('stations/Index', [
            'title' => 'Автовокзалы и автокассы',
            'stations' => $stations,
            'description' => $metaDescription,
        ]);
    }

    /**
     * Показывает информацию об одной станции по ключу
     */
    public function show(string $stationKey): Response
    {
    $stations = Config::get('stations.autovokzal.stations');

    if (!array_key_exists($stationKey, $stations)) {
        throw new NotFoundHttpException("Станция не найдена");
    }

    $station = $stations[$stationKey];

    // Для конкретных станций задаём отдельный meta description
    if ($stationKey === 'ulan-ude-baikal') {
        $metaDescription = 'Автовокзал в Улан-Удэ «Байкал». Расписание автобусов автовокзала «Байкал» в Улан-Удэ. Купить билеты онлайн в Иркутск, Читу, Кяхту, Закаменск, Турунтаево, Горячинск, Хоринск, Максимиху, Усть-Баргузин и ещё более 80 населённых пунктов.';
    } elseif ($stationKey === 'ulanude-railroad') {
        $metaDescription = 'Автовокзал в Улан-Удэ на ЖД вокзале. Онлайн билеты и расписание автобусов с ЖД вокзала Улан-Удэ.';
    } else {
        // Для остальных станций формируем динамически
        $name = $station['name'] ?? 'Станция';

        if (!empty($station['routes']) && is_array($station['routes'])) {
            $routesList = implode(', ', $station['routes']);
        } else {
            $routesList = 'различные города и населённые пункты';
        }

        $metaDescription = sprintf(
            'Расписание автобусов %s. Купить билеты онлайн в %s.',
            $name,
            $routesList
        );
    }

    return Inertia::render('stations/Show', [
        'title' => $station['name'] ?? 'Станция',
        'station' => $station,
        'stationKey' => $stationKey,
        'description' => $metaDescription,
    ]);
    }
}
