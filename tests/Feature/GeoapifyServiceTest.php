<?php

namespace Tests\Feature;

use App\Services\GeoapifyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class GeoapifyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_geoapify_service_normalizes_geojson_coordinates(): void
    {
        config()->set('services.geoapify.key', 'test-key');
        Cache::flush();
        Http::fake([
            'https://api.geoapify.com/v2/places*' => Http::response([
                'features' => [[
                    'properties' => [
                        'place_id' => 'test-place-123',
                        'name' => 'Parc du Bardo',
                        'formatted' => 'Le Bardo, Tunis, Tunisia',
                        'categories' => ['leisure.park'],
                    ],
                    'geometry' => ['coordinates' => [10.14, 36.809]],
                ]],
            ]),
        ]);

        $results = app(GeoapifyService::class)->searchNearbyPlaces(36.809, 10.14, 3000);

        $this->assertSame('test-place-123', $results[0]['place_id']);
        $this->assertSame('Parc du Bardo', $results[0]['nom']);
        $this->assertSame(36.809, $results[0]['latitude']);
        $this->assertSame(10.14, $results[0]['longitude']);
        Http::assertSent(fn ($request) => $request['filter'] === 'circle:10.14,36.809,3000');
    }

    public function test_geoapify_service_hides_remote_errors(): void
    {
        config()->set('services.geoapify.key', 'test-key');
        Cache::flush();
        Http::fake(['https://api.geoapify.com/v2/places*' => Http::response([], 429)]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Les suggestions Geoapify sont temporairement indisponibles.');

        app(GeoapifyService::class)->searchNearbyPlaces(36.809, 10.14);
    }
}
