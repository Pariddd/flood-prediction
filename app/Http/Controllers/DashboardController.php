<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenWeatherService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request, OpenWeatherService $weather)
    {
        $cityInput = $request->get('city', 'Jepara');

        $location = $weather->geocode($cityInput);

        if (!$location) {
            return redirect('/')
                ->with('error', 'Lokasi tidak ditemukan');
        }

        $current = $weather->currentWeather($location['lat'], $location['lon']);
        $forecastRaw = $weather->forecast($location['lat'], $location['lon']);

        $rain24h = 0;
        $rain3d  = 0;
        $rain5d  = 0;

        $now = now();

        foreach ($forecastRaw['list'] as $item) {

            $time = \Carbon\Carbon::createFromTimestamp($item['dt']);
            $rain = $item['rain']['3h'] ?? 0;

            if ($time->lessThanOrEqualTo($now->copy()->addDay())) {
                $rain24h += $rain;
            }

            if ($time->lessThanOrEqualTo($now->copy()->addDays(3))) {
                $rain3d += $rain;
            }

            $rain5d += $rain;
        }

        $riskLevel = 'Aman';
        $riskColor = 'green';
        $riskMessage = 'Kondisi aman, tidak terdeteksi potensi banjir.';

        if ($rain24h >= 100 || $rain3d >= 200) {
            $riskLevel = 'Bahaya';
            $riskColor = 'red';
            $riskMessage = 'Curah hujan sangat tinggi, potensi banjir besar.';
        } elseif ($rain24h >= 50 || $rain3d >= 100) {
            $riskLevel = 'Waspada';
            $riskColor = 'yellow';
            $riskMessage = 'Curah hujan meningkat, waspada potensi genangan.';
        }

        if ($current['main']['humidity'] >= 85 && $riskLevel !== 'Bahaya') {
            $riskLevel = 'Bahaya';
            $riskColor = 'red';
            $riskMessage = 'Kelembapan sangat tinggi, tanah jenuh air.';
        }

        $forecastDaily = [];

        foreach ($forecastRaw['list'] as $item) {

            $date = \Carbon\Carbon::createFromTimestamp($item['dt'])->format('D');

            if (!isset($forecastDaily[$date])) {
                $forecastDaily[$date] = [
                    'day'     => $date,
                    'temp'    => round($item['main']['temp']),
                    'weather' => $item['weather'][0]['main'],
                ];
            }

            if (count($forecastDaily) >= 4) {
                break;
            }
        }

        $forecastDaily = array_values($forecastDaily);

        $data = [
            'temperature' => round($current['main']['temp']),
            'humidity'    => $current['main']['humidity'],
            'pressure'    => $current['main']['pressure'],
            'wind'        => round($current['wind']['speed'], 1),
            'weather'     => $current['weather'][0]['main'],
            'description' => $current['weather'][0]['description'],
            'rain'        => $current['rain']['1h'] ?? 0,
            'day'         => \Carbon\Carbon::createFromTimestamp($current['dt'])->format('l'),
            'date'        => \Carbon\Carbon::createFromTimestamp($current['dt'])->format('d F Y'),
            'city'        => $location['name'],
            'country'     => $location['country'],
            'rain24h' => round($rain24h, 1),
            'rain3d'  => round($rain3d, 1),
            'rain5d'  => round($rain5d, 1),
            'forecast' => $forecastDaily,
            'riskLevel'   => $riskLevel,
            'riskColor'   => $riskColor,
            'riskMessage' => $riskMessage,

        ];

        return view('dashboard', $data);
    }
}
