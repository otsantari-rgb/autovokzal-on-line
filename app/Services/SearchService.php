<?php

namespace App\Services;

use App\Clients\BiletAvtoApiClient;
use App\Models\SearchRoutesCounter;
use Carbon\Carbon;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class SearchService
{
    protected BiletAvtoApiClient $client;

    public function __construct(BiletAvtoApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ConnectionException
     */
    public function searchRides($from, $to, $date): array
    {
        $result = $this->client->search($from, $to, $date);
        if (! empty($result)) {
            $route = SearchRoutesCounter::where('from', $from)->where('to', $to)->first();

            if ($route) {
                $route->increment('quantity');
            } else {
                SearchRoutesCounter::create([
                    'from' => $from,
                    'to' => $to,
                    'quantity' => 1,
                ]);
            }
        }

        $filteredResult = array_filter($result, function ($sheet) {
            if (!is_array($sheet) || !isset($sheet['places']['freePlaces']) || !is_array($sheet['places']['freePlaces'])) {
                return false; // Пропускаем элементы с некорректной структурой
            }

            return count($sheet['places']['freePlaces']) > 0;
        });

        return array_map(function ($sheet) {
            //            $freePlaces = min(count($sheet['places']['freePlaces']), 10);
            $needDocs = ! empty($sheet['mintrans_transfer']) && $sheet['mintrans_transfer'] == 1;

            return [
                'id' => $sheet['rideId'],
                'name' => $sheet['routeName'],
                'departure_station' => $sheet['departureCity'],
                'departure_date' => Carbon::parse($sheet['departureDate'])->format('d.m.Y'),
                'departure_time' => Carbon::parse($sheet['departureTime'])->format('H:i'),
                'departure_address' => $sheet['departureStation'],
                'arrival_station' => $sheet['arrivalCity'],
                'arrival_date' => Carbon::parse($sheet['arrivalDate'])->format('d.m.Y'),
                'arrival_time' => Carbon::parse($sheet['arrivalTime'])->format('H:i'),
                'arrival_address' => $sheet['arrivalStation'],
                'need_docs' => $needDocs,
                'freePlaces' => count($sheet['places']['freePlaces']), // $freePlaces
                'carrier' => $sheet['carrier'],
                'carrier_tin' => '',
                'price' => floatval($sheet['price']),
                'price_id' => $sheet['priceId'],
            ];
        }, $filteredResult);
    }
}
