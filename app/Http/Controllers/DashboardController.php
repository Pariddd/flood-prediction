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

        $data = [
            'temperature' => round($current['main']['temp']),
            'humidity'    => $current['main']['humidity'],
            'pressure'    => $current['main']['pressure'],
            'wind'        => round($current['wind']['speed'], 1),
            'weather'     => $current['weather'][0]['main'],
            'description' => $current['weather'][0]['description'],
            'rain'        => $current['rain']['1h'] ?? 0,
            'day'         => Carbon::createFromTimestamp($current['dt'])->format('l'),
            'date'        => Carbon::createFromTimestamp($current['dt'])->format('d F Y'),
            'city'        => $location['name'],
            'country'     => $location['country'],
        ];

        return view('dashboard', $data);
    }
}
