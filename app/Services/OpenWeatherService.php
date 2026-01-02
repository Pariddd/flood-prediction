<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenWeatherService
{
  protected string $apiKey;
  protected string $baseUrl;

  public function __construct()
  {
    $this->apiKey = config('services.openweather.key');
    $this->baseUrl = config('services.openweather.url');
  }

  public function currentWeather(float $lat, float $lon): array
  {
    $response = Http::get($this->baseUrl . '/weather', [
      'lat' => $lat,
      'lon' => $lon,
      'units' => 'metric',
      'appid' => $this->apiKey,
    ]);

    return $response->json();
  }

  public function forecast(float $lat, float $lon): array
  {
    $response = Http::get($this->baseUrl . '/forecast', [
      'lat' => $lat,
      'lon' => $lon,
      'units' => 'metric',
      'appid' => $this->apiKey,
    ]);

    return $response->json();
  }

  public function geocode(string $city): ?array
  {
    $response = Http::get('http://api.openweathermap.org/geo/1.0/direct', [
      'q' => $city,
      'limit' => 1,
      'appid' => $this->apiKey,
    ]);

    $data = $response->json();

    if (empty($data)) {
      return null;
    }

    return [
      'lat' => $data[0]['lat'],
      'lon' => $data[0]['lon'],
      'name' => $data[0]['name'],
      'country' => $data[0]['country'] ?? '',
    ];
  }

  public function elevation($lat, $lon)
  {
    try {
      $response = Http::timeout(10)->get(
        'https://api.open-elevation.com/api/v1/lookup',
        [
          'locations' => "{$lat},{$lon}"
        ]
      );

      if ($response->failed()) {
        return 0;
      }

      return $response->json()['results'][0]['elevation'] ?? 0;
    } catch (\Exception $e) {
      return 0;
    }
  }
}
