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
}
