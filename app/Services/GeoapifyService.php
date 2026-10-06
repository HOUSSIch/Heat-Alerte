<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeoapifyService
{
    private const CATEGORIES = 'leisure.park,commercial.shopping_mall,activity.community_center';

    /** @return array<int, array{place_id: string, nom: string, adresse: string, latitude: float, longitude: float, categories: array}> */
    public function searchNearbyPlaces(float $latitude, float $longitude, int $radius = 3000): array
    {
        $key = config('services.geoapify.key');

        if (blank($key)) {
            throw new RuntimeException('Les suggestions Geoapify sont temporairement indisponibles.');
        }

        $cacheKey = 'geoapify:'.sha1(implode('|', [$latitude, $longitude, $radius, self::CATEGORIES]));

        return Cache::remember($cacheKey, now()->addMinutes(20), function () use ($latitude, $longitude, $radius, $key) {
            try {
                $response = Http::timeout(8)->get('https://api.geoapify.com/v2/places', [
                    'categories' => self::CATEGORIES,
                    'filter' => "circle:{$longitude},{$latitude},{$radius}",
                    'bias' => "proximity:{$longitude},{$latitude}",
                    'limit' => 20,
                    'lang' => 'fr',
                    'apiKey' => $key,
                ]);

                if (! $response->successful() || ! is_array($response->json())) {
                    throw new RuntimeException();
                }

                return collect(data_get($response->json(), 'features', []))
                    ->map(function ($feature) {
                        $properties = data_get($feature, 'properties', []);
                        $coordinates = data_get($feature, 'geometry.coordinates', []);
                        $longitude = $coordinates[0] ?? null;
                        $latitude = $coordinates[1] ?? null;

                        if (! is_array($properties) || ! is_numeric($longitude) || ! is_numeric($latitude)) {
                            return null;
                        }

                        $categories = data_get($properties, 'categories', []);

                        return [
                            'place_id' => (string) (data_get($properties, 'place_id') ?? ''),
                            'nom' => (string) (data_get($properties, 'name') ?: data_get($properties, 'address_line1') ?: 'Lieu sans nom'),
                            'adresse' => (string) (data_get($properties, 'formatted') ?: data_get($properties, 'address_line1') ?: 'Adresse non renseignée'),
                            'latitude' => (float) $latitude,
                            'longitude' => (float) $longitude,
                            'categories' => is_array($categories) ? array_values($categories) : [],
                        ];
                    })
                    ->filter(fn ($place) => $place && $place['place_id'] !== '')
                    ->values()
                    ->all();
            } catch (\Throwable) {
                throw new RuntimeException('Les suggestions Geoapify sont temporairement indisponibles.');
            }
        });
    }
}
