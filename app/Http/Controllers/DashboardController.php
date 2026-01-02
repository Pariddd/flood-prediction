<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenWeatherService;
use App\Services\FloodMlService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request, OpenWeatherService $weather, FloodMlService $ml)
    {
        $cityInput = $request->get('city', 'Jepara');

        $location = $weather->geocode($cityInput);
        if (!$location) {
            return redirect('/')->with('error', 'Lokasi tidak ditemukan');
        }

        $current     = $weather->currentWeather($location['lat'], $location['lon']);
        $forecastRaw = $weather->forecast($location['lat'], $location['lon']);

        $rain24h = 0;
        $rain3d  = 0;
        $rain7d  = 0;

        $now = now();

        foreach ($forecastRaw['list'] as $item) {
            $time = Carbon::createFromTimestamp($item['dt']);
            $rain = $item['rain']['3h'] ?? 0;

            if ($time->lessThanOrEqualTo($now->copy()->addDay())) {
                $rain24h += $rain;
            }

            if ($time->lessThanOrEqualTo($now->copy()->addDays(3))) {
                $rain3d += $rain;
            }

            $rain7d += $rain;
        }

        $elevation = $weather->elevation(
            $location['lat'],
            $location['lon']
        ) ?? 0;

        $mlPayload = [
            'rain_24h'    => round($rain24h, 1),
            'rain_3d'     => round($rain3d, 1),
            'rain_7d'     => round($rain7d, 1),
            'humidity'    => $current['main']['humidity'],
            'temperature' => round($current['main']['temp'], 1),
            'pressure'    => $current['main']['pressure'],
            'wind_speed'  => round($current['wind']['speed'], 1),
            'elevation'   => $elevation,
        ];

        $mlResult = $ml->predict($mlPayload);

        $riskLevel   = 'Aman';
        $riskColor   = 'green';
        $riskMessage = 'Kondisi relatif aman.';

        if ($mlResult) {
            $riskLevel = $mlResult['risk'];
        } else {
            if ($rain24h >= 100 || $rain3d >= 200) {
                $riskLevel = 'Bahaya';
            } elseif ($rain24h >= 50 || $rain3d >= 100) {
                $riskLevel = 'Waspada';
            }
        }

        if ($riskLevel === 'Bahaya') {
            $riskColor   = 'red';
            $riskMessage = 'Risiko banjir tinggi, curah hujan ekstrem.';
        } elseif ($riskLevel === 'Waspada') {
            $riskColor   = 'yellow';
            $riskMessage = 'Curah hujan meningkat, harap waspada.';
        }

        $forecast = [];

        foreach ($forecastRaw['list'] as $item) {
            $dateKey = Carbon::createFromTimestamp($item['dt'])->format('Y-m-d');

            if (!isset($forecast[$dateKey])) {
                $forecast[$dateKey] = [
                    'day'  => Carbon::createFromTimestamp($item['dt'])->format('D'),
                    'temp' => round($item['main']['temp']),
                    'icon' => $item['weather'][0]['icon'],
                ];
            }

            if (count($forecast) >= 4) {
                break;
            }
        }

        $forecast = array_values($forecast);

        return view('dashboard', [
            'temperature' => round($current['main']['temp']),
            'humidity'    => $current['main']['humidity'],
            'pressure'    => $current['main']['pressure'],
            'wind'        => round($current['wind']['speed'], 1),
            'weather'     => $current['weather'][0]['main'],
            'description' => $current['weather'][0]['description'],
            'currentIcon' => $current['weather'][0]['icon'],
            'rain'        => $current['rain']['1h'] ?? 0,

            'day'         => Carbon::createFromTimestamp($current['dt'])->format('l'),
            'date'        => Carbon::createFromTimestamp($current['dt'])->format('d F Y'),
            'city'        => $location['name'],
            'country'     => $location['country'],

            'rain24h'     => round($rain24h, 1),
            'rain3d'      => round($rain3d, 1),
            'rain7d'      => round($rain7d, 1),
            'elevation'   => $elevation,

            'forecast'    => $forecast,

            'riskLevel'   => $riskLevel,
            'riskColor'   => $riskColor,
            'riskMessage' => $riskMessage,
            'confidence'  => $mlResult['confidence'] ?? null,
        ]);
    }
}
