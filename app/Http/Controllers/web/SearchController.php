<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Services\SearchService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Station;
use App\Models\MoreRoute;

class SearchController extends Controller
{
    protected SearchService $searchService;
    
    private const MAX_DAYS_TO_CHECK = 14;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(SearchRequest $request): Response
{
    try {
        $from = $request->from;
        $to = $request->to;
        $date = $request->date ?? now()->format('d.m.Y');

        $sheets = $this->searchService->searchRides($from, $to, $date);
        $sheets = $this->sortSheets($sheets);

        $alternativeDate = null;
        $alternativeSheets = [];

        if (empty($sheets)) {
            $startDate = Carbon::createFromFormat('d.m.Y', $date);
            $nearestDate = $this->findNearestAvailableDate($from, $to, $startDate);

            if ($nearestDate) {
                $alternativeDate = $nearestDate->format('d.m.Y');
                $alternativeSheets = $this->sortSheets(
                    $this->searchService->searchRides($from, $to, $alternativeDate)
                );
            }
        }

        return Inertia::render('search/SearchResults', [
            'title' => 'Результаты поиска',
            'description' => "Автовокзал $from - $to. Бронируйте автобусные билеты онлайн!",
            'success' => true,
            'data' => [
                'sheets' => $sheets,
                'from' => $from,
                'to' => $to,
                'date' => $date,
                'mapData' => $this->prepareMapData("$from - $to"),
                'popularRoutes' => $this->getPopularRoutes(),
                'nearest' => [
                    'date' => $alternativeDate,
                    'sheets' => $alternativeSheets,
                ]
            ]
        ]);

    } catch (Exception $e) {
        Log::error('Ошибка в поиске рейсов', ['exception' => $e]);

        return Inertia::render('search/SearchResults', [
            'title' => 'Результаты поиска',
            'success' => false,
            'error' => 'Произошла ошибка'
        ]);
    }
}


    public function directional(string $direction): Response
{
    try {
        $cities = explode('--', $direction);
        if (count($cities) !== 2) {
            abort(404, 'Некорректный формат маршрута');
        }

        [$startTranslated, $endTranslated] = $cities;

        $route = MoreRoute::where('start_translated', $startTranslated)
            ->where('end_translated', $endTranslated)
            ->first();

        if (!$route) {
            abort(404, 'Маршрут не найден');
        }

        $from = $route->start;
        $to = $route->end;
        $date = now()->format('d.m.Y');

        $sheets = $this->searchService->searchRides($from, $to, $date);
        $sheets = $this->sortSheets($sheets);

        $alternativeDate = null;
        $alternativeSheets = [];

        if (empty($sheets)) {
            $startDate = Carbon::createFromFormat('d.m.Y', $date);
            $nearestDate = $this->findNearestAvailableDate($from, $to, $startDate);

            if ($nearestDate) {
                $alternativeDate = $nearestDate->format('d.m.Y');
                $alternativeSheets = $this->sortSheets(
                    $this->searchService->searchRides($from, $to, $alternativeDate)
                );
            }
        }

        return Inertia::render('search/SearchResults', [
            'title' => "Расписание автобусов $from - $to",
            'description' => "Автовокзал $from - $to. Бронируйте автобусные билеты онлайн!",
            'success' => true,
            'data' => [
                'sheets' => $sheets,
                'from' => $from,
                'to' => $to,
                'date' => $date,
                'mapData' => $this->prepareMapData($direction),
                'popularRoutes' => $this->getPopularRoutes(),
                'nearest' => [
                    'date' => $alternativeDate,
                    'sheets' => $alternativeSheets,
                ]
            ]
        ]);

    } catch (Exception $e) {
        Log::error('Ошибка в поиске рейсов', [
            'exception' => $e,
            'direction' => $direction
        ]);

        return Inertia::render('search/SearchResults', [
            'title' => 'Ошибка поиска',
            'success' => false,
            'error' => $e->getMessage() ?? 'Произошла ошибка',
            'data' => [
                'sheets' => [],
                'from' => '',
                'to' => '',
                'date' => null,
                'nearest' => [
                    'date' => null,
                    'sheets' => [],
                ]
            ]
        ]);
    }
}



    /**
     * Подготовка данных для карты
     */
    private function prepareMapData(string $direction): array
    {
        if(str_contains($direction, "--")) {
            $cities = explode('--', $direction);
            if (count($cities) !== 2) {
                return [];
            }
            [$startTranslated, $endTranslated] = $cities;
            $route = MoreRoute::where('start_translated', $startTranslated)
                ->where('end_translated', $endTranslated)
                ->first();
        } else if(str_contains($direction, " - ")) {
            $cities = explode(' - ', $direction);
            if (count($cities) !== 2) {
                return [];
            }
            [$startTranslated, $endTranslated] = $cities;
            $route = MoreRoute::where('start', $startTranslated)
                ->where('end', $endTranslated)
                ->first();
        }
        
        Log::info($cities);

        if (!$route) {
            return [];
        }

        return [
            'fromCoords' => [
                $route->coordinates_from_lat,
                $route->coordinates_from_long,
            ],
            'toCoords' => [
                $route->coordinates_to_lat,
                $route->coordinates_to_long,
            ],
            'fromName' => $route->start,
            'toName' => $route->end,
            'time' => null, // можно подставить, если есть
            'lunch' => null,
            'phone' => null,
        ];
    }

    /**
     * Получение списка популярных маршрутов
     */
    private function getPopularRoutes(): array
    {
        $routes = MoreRoute::where('is_popular', true)
            ->select([
                'route_name',
                'start',
                'end',
                'start_translated',
                'end_translated',
                'coordinates_from_lat',
                'coordinates_from_long',
                'coordinates_to_lat',
                'coordinates_to_long',
                'price',
            ])
            ->get();

        $result = [];
                
        foreach ($routes as $route) {
            $result[] = [
                'key' => $route->route_name,
                'name' => "{$route->start} — {$route->end}",
                'price' => $route->price ?? null,
                'coordinates_from' => [
                    'lat' => $route->coordinates_from_lat,
                    'long' => $route->coordinates_from_long,
                ],
                'coordinates_to' => [
                    'lat' => $route->coordinates_to_lat,
                    'long' => $route->coordinates_to_long,
                ],
            ];
        }
        
        return $result;
    }
    /**
     * Получение названия маршрута по ключу
     */
    private function getRoute(string $routeKey): ?string
    {
        // вернет название маршрута, например "Улан-Удэ - Хоринск"
        return config("popular_routes.$routeKey.name");
    }

    /**
     * Поиск ближайшей даты с доступными рейсами
     */
    private function findNearestAvailableDate(string $from, string $to, Carbon $startDate): ?Carbon
    {
        $period = CarbonPeriod::create(
            $startDate->copy()->addDay(),
            $startDate->copy()->addDays(self::MAX_DAYS_TO_CHECK)
        );

        foreach ($period as $date) {
            $sheets = $this->searchService->searchRides($from, $to, $date->format('d.m.Y'));
            if (!empty($sheets)) {
                return $date;
            }
        }

        return null;
    }

    /**
     * Сортировка рейсов по времени отправления
     */
    private function sortSheets(array $sheets): array
    {
        if (empty($sheets)) {
            return [];
        }

        usort($sheets, function ($a, $b) {
            try {
                return Carbon::parse($a['departure_time'])->timestamp <=> 
                       Carbon::parse($b['departure_time'])->timestamp;
            } catch (Exception $e) {
                return 0;
            }
        });

        return $sheets;
    }
}





