<?php

namespace Tests\Feature;

use App\Models\Quartier;
use App\Models\User;
use App\Services\NominatimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NominatimTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_is_geocoded_when_creating_signalement(): void
    {
        $q = Quartier::create(['nom' => 'Bardo', 'ville' => 'Tunis', 'latitude' => 36.809, 'longitude' => 10.140, 'actif' => true]);
        $u = User::factory()->create(['quartier_id' => $q->id]);
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '36.8090', 'lon' => '10.1400', 'display_name' => 'Le Bardo, Tunis, Tunisia']])]);
        $this->actingAs($u)->post('/signalements', ['quartier_id' => $q->id, 'titre' => 'Coupure électrique', 'description' => 'Coupure électrique constatée dans le quartier.', 'adresse' => 'Bardo'])->assertSessionHas('success');
        $this->assertDatabaseHas('signalements', ['user_id' => $u->id, 'latitude' => 36.8090, 'longitude' => 10.1400]);
    }

    public function test_signalement_is_saved_when_geocoding_fails(): void
    {
        $q = Quartier::create(['nom' => 'Bardo', 'ville' => 'Tunis', 'latitude' => 36.809, 'longitude' => 10.140, 'actif' => true]);
        $u = User::factory()->create(['quartier_id' => $q->id]);
        Http::fake(['*' => Http::response([], 500)]);
        $this->actingAs($u)->post('/signalements', ['quartier_id' => $q->id, 'titre' => 'Coupure électrique', 'description' => 'Coupure électrique constatée dans le quartier.', 'adresse' => 'Bardo'])->assertSessionHas('success');
        $this->assertDatabaseHas('signalements', ['user_id' => $u->id, 'titre' => 'Coupure électrique']);
    }

    public function test_search_returns_normalized_address_suggestions(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([
            ['lat' => '36.8', 'lon' => '10.1', 'display_name' => 'Bardo, Tunis'],
            ['lat' => '36.9', 'lon' => '10.2'],
        ])]);

        $results = app(NominatimService::class)->search('Bardo');

        $this->assertSame('Bardo, Tunis', $results[0]['label']);
        $this->assertSame(36.8, $results[0]['latitude']);
        $this->assertSame(10.2, $results[1]['longitude']);
    }

    public function test_search_skips_short_queries_and_reports_remote_failures(): void
    {
        $service = app(NominatimService::class);

        $this->assertSame([], $service->search('ab'));

        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([], 500)]);

        $this->expectException(\RuntimeException::class);
        $service->search('Bardo');
    }
}
