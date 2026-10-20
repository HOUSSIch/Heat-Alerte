<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NominatimService
{
    /** @return list<array{label: string, latitude: float, longitude: float}> */
    public function search(string $query): array
    {
        if (mb_strlen(trim($query)) < 3) {
            return [];
        }

        try {
            $response = Http::timeout(8)->withHeaders(['User-Agent' => 'HeatAlert/1.0'])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 5,
            ]);

            if (! $response->successful() || ! is_array($response->json())) {
                throw new \RuntimeException('Recherche d’adresse temporairement indisponible.');
            }

            return collect($response->json())->filter(fn ($item) => isset($item['lat'], $item['lon']))->map(fn ($item) => [
                'label' => (string) ($item['display_name'] ?? $query),
                'latitude' => (float) $item['lat'],
                'longitude' => (float) $item['lon'],
            ])->values()->all();
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Recherche d’adresse temporairement indisponible.', previous: $exception);
        }
    }

    public function geocode(string $address): ?array
    {
        return Cache::remember('nominatim:'.hash('sha256', $address), now()->addMinutes(30), function () use ($address) {
            try {
                $r = Http::timeout(8)
                    ->withHeaders(['User-Agent' => 'HeatAlert/1.0'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $address,
                        'format' => 'json',
                        'limit' => 1,
                    ]);
                $d = $r->json('0');

                if (! $r->successful() || ! is_array($d) || ! isset($d['lat'], $d['lon'])) {
                    return null;
                }

                return [
                    'latitude' => (float) $d['lat'],
                    'longitude' => (float) $d['lon'],
                    'display_name' => $d['display_name'] ?? $address,
                ];
            } catch (\Throwable) {
                return null;
            }
        });
    }
}
