<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\MoreRoute;

class MoreRoutesController extends Controller
{
    public function index()
{
    return response()->json(
        MoreRoute::select([
            'id',
            'start',
            'end',
            'route_name',
            'start_translated',
            'end_translated',
            'coordinates_from_lat',
            'coordinates_from_long',
            'coordinates_to_lat',
            'coordinates_to_long'
        ])->get()->map(function ($route) {
            $slug = strtolower(
                str_replace([' ', 'ё', 'Ё'], ['-', 'e', 'e'], $route->start_translated)
            ) . '--' . strtolower(
                str_replace([' ', 'ё', 'Ё'], ['-', 'e', 'e'], $route->end_translated)
            );

            $route->slug = $slug;
            return $route;
        })
    );
}
}