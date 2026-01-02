<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenWeatherService;
use App\Services\FloodMlService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request, OpenWeatherService $weather, FloodMlService $ml)
    {
        $cityInput = $request->get('city', 'Banda Aceh');

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

    public function heatmap(
        Request $request,
        OpenWeatherService $weather,
        FloodMlService $ml
    ) {
        set_time_limit(180);

        $cityInput = $request->get('city', 'Banda Aceh');

        $location = $weather->geocode($cityInput);
        if (!$location) {
            return redirect('/')->with('error', 'Lokasi tidak ditemukan');
        }

        $lat = $location['lat'];
        $lon = $location['lon'];

        $points = [];
        $gridSize = 0.02;
        $range = 1;

        for ($i = -$range; $i <= $range; $i++) {
            for ($j = -$range; $j <= $range; $j++) {

                $pLat = $lat + ($i * $gridSize);
                $pLon = $lon + ($j * $gridSize);

                try {
                    $cacheKey = "weather_{$pLat}_{$pLon}_" . now()->format('YmdH');

                    $weatherData = Cache::remember($cacheKey, 3600, function () use ($weather, $pLat, $pLon) {
                        $current = $weather->currentWeather($pLat, $pLon);
                        $forecast = $weather->forecast($pLat, $pLon);
                        $elevation = $weather->elevation($pLat, $pLon) ?? 0;

                        return compact('current', 'forecast', 'elevation');
                    });

                    $current = $weatherData['current'];
                    $forecast = $weatherData['forecast'];
                    $elevation = $weatherData['elevation'];

                    if (!$current || !$forecast) continue;
                } catch (\Exception $e) {
                    \Log::error("Weather API Error: " . $e->getMessage());
                    continue;
                }

                $rain24h = 0;
                $rain3d  = 0;
                $rain7d  = 0;
                $now = now();

                foreach ($forecast['list'] as $item) {
                    $time = \Carbon\Carbon::createFromTimestamp($item['dt']);
                    $rain = $item['rain']['3h'] ?? 0;

                    if ($time->lessThanOrEqualTo($now->copy()->addDay())) {
                        $rain24h += $rain;
                    }
                    if ($time->lessThanOrEqualTo($now->copy()->addDays(3))) {
                        $rain3d += $rain;
                    }
                    if ($time->lessThanOrEqualTo($now->copy()->addDays(7))) {
                        $rain7d += $rain;
                    }
                }

                $payload = [
                    'rain_24h'    => round($rain24h, 1),
                    'rain_3d'     => round($rain3d, 1),
                    'rain_7d'     => round($rain7d, 1),
                    'humidity'    => $current['main']['humidity'],
                    'temperature' => round($current['main']['temp'], 1),
                    'pressure'    => $current['main']['pressure'],
                    'wind_speed'  => round($current['wind']['speed'], 1),
                    'elevation'   => $elevation,
                ];

                try {
                    $mlCacheKey = 'ml_' . md5(json_encode($payload));

                    $mlResult = Cache::remember($mlCacheKey, 3600, function () use ($ml, $payload) {
                        return $ml->predict($payload);
                    });

                    $risk = $mlResult['risk'] ?? 'Aman';
                    $probability = $mlResult['probability'] ?? 0;
                } catch (\Exception $e) {

                    \Log::error("ML API Error: " . $e->getMessage());

                    $risk = 'Aman';
                    $probability = 0;
                }


                $color = match ($risk) {
                    'Bahaya'  => '#ef4444',
                    'Waspada' => '#f59e0b',
                    default   => '#10b981',
                };

                $points[] = [
                    'lat'         => $pLat,
                    'lon'         => $pLon,
                    'risk'        => $risk,
                    'color'       => $color,
                    'probability' => round($probability * 100, 1),
                    'data'        => $payload,
                ];
            }
        }

        $stats = [
            'total' => count($points),
            'bahaya' => collect($points)->where('risk', 'Bahaya')->count(),
            'waspada' => collect($points)->where('risk', 'Waspada')->count(),
            'aman' => collect($points)->where('risk', 'Aman')->count(),
        ];

        return view('heatmap', [
            'city'   => $location['name'],
            'lat'    => $lat,
            'lon'    => $lon,
            'points' => $points,
            'stats'  => $stats,
        ]);
    }
}
