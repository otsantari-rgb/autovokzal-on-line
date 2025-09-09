<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Models\Station;
use App\Services\SearchService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SearchController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(SearchRequest $request): JsonResponse
    {
        try {
            // Получение данных
            $from = $request->from;
            $to = $request->to;
            $date = $request->date;

            // Поиск рейсов
            $sheets = $this->searchService->searchRides($from, $to, $date);

            // Сортировка результатов
            usort($sheets, fn ($a, $b) => Carbon::parse($a['departure_time'])->gt(Carbon::parse($b['departure_time'])));

            // Возврат результата
            return response()->json([
                'success' => true,
                'data' => [
                    'sheets' => $sheets,
                    'from' => $from,
                    'to' => $to,
                    'date' => $date,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Ошибка в поиске рейсов', ['exception' => $e]);

            return response()->json(['success' => false, 'error' => 'Произошла ошибка'], 500);
        }
    }

    public function searchStations(Request $request): JsonResponse
    {
        try {
            $query = trim($request->input('query', ''));
            $type = $request->input('type', 'from');
            if (! in_array($type, ['from', 'to'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Неверный тип поиска',
                ], 400);
            }
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'suggestions' => [],
                ]);
            }

            $suggestions = Station::leftJoin('search_routes_counters', function ($join) use ($type) {
                $join->on('stations.city', '=', "search_routes_counters.$type");
            })
                ->whereRaw('LOWER(stations.city) LIKE LOWER(?)', ["%$query%"])
                ->select(
                    'stations.id',
                    'stations.city',
                    DB::raw('COALESCE(SUM(search_routes_counters.quantity), 0) as total_quantity')
                )
                ->groupBy('stations.id', 'stations.city')
                ->orderByDesc('total_quantity')
                ->orderBy('stations.city')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions,
            ]);
        } catch (Exception $e) {
            Log::error('Ошибка при поиске станций', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'error' => 'Произошла ошибка при поиске станций',
            ], 500);
        }
    }
}
