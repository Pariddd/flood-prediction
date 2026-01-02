<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FloodMlService
{
  public function predict(array $payload)
  {
    $response = Http::timeout(10)
      ->post(config('services.ml.url'), $payload);

    if ($response->failed()) {
      return null;
    }

    return $response->json();
  }
}
