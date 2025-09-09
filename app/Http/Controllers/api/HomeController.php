<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\MoreRoute;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json(config('stations.autovokzal.stations'));
    }

    public function cities(): JsonResponse
    {
        return response()->json(config('footer.arrivals'));
    }

    public function popularRoutes(): JsonResponse
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

        return response()->json($result);
    }

    public function moreRoutes(): JsonResponse
    {
        $routes = MoreRoute::orderBy('start', 'asc')->get();

        return response()->json($routes);
    }
}
