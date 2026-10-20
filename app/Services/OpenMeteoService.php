<?php

namespace App\Services;

use App\Models\Quartier;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenMeteoService
{
    /** @return array<string, mixed> */
    public function weather(Quartier $quartier): array
    {
        return $this->forecast($quartier);
    }

    /** @return array<string, mixed> */
    public function forecast(Quartier $quartier, bool $refresh = false): array
    {
        $key = "open-meteo:{$quartier->id}:forecast";
        if (! $refresh && Cache::has($key)) {
            return Cache::get($key);
        }

        try {
            $response = Http::timeout(8)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $quartier->latitude,
                'longitude' => $quartier->longitude,
                'current' => 'temperature_2m',
                'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum',
                'forecast_days' => 7,
                'timezone' => 'Africa/Tunis',
            ]);
            if (! $response->successful()) {
                throw new RuntimeException;
            }

            $data = $response->json();
            $temperature = data_get($data, 'current.temperature_2m');
            $maximums = data_get($data, 'daily.temperature_2m_max');
            $minimums = data_get($data, 'daily.temperature_2m_min');
            $dates = data_get($data, 'daily.time');
            if (! is_numeric($temperature) || ! is_array($maximums) || ! is_array($minimums)) {
                throw new RuntimeException;
            }

            $days = collect($maximums)->take(7)->map(function ($maximum, int $index) use ($minimums, $dates, $data): array {
                return [
                    'date' => $dates[$index] ?? now()->addDays($index)->toDateString(),
                    'temperature_max' => (float) $maximum,
                    'temperature_min' => (float) ($minimums[$index] ?? $maximum),
                    'precipitation' => (float) data_get($data, "daily.precipitation_sum.{$index}", 0),
                ];
            })->all();
            if ($days === []) {
                throw new RuntimeException;
            }

            $forecast = [
                'temperature' => (float) $temperature,
                'temperature_max' => $days[0]['temperature_max'],
                'temperature_min' => $days[0]['temperature_min'],
                'days' => $days,
            ];
            Cache::put($key, $forecast, now()->addMinutes(10));

            return $forecast;
        } catch (\Throwable) {
            throw new RuntimeException('Les données météo sont temporairement indisponibles.');
        }
    }
}
