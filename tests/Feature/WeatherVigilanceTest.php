<?php

namespace Tests\Feature;

use App\Models\AlerteMeteo;
use App\Models\Quartier;
use App\Models\User;
use App\Notifications\WeatherVigilanceNotification;
use App\Services\OpenMeteoService;
use App\Services\WeatherRiskService;
use App\Services\WeatherVigilanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherVigilanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_open_meteo_extracts_seven_days_and_caches_the_response(): void
    {
        $quartier = Quartier::create(['nom' => 'Bardo', 'ville' => 'Tunis', 'latitude' => 36.8, 'longitude' => 10.1, 'actif' => true]);
        Http::fake(['api.open-meteo.com/*' => Http::response(['current' => ['temperature_2m' => 39], 'daily' => ['time' => collect(range(0, 6))->map(fn ($day) => today()->addDays($day)->toDateString())->all(), 'temperature_2m_max' => [39, 42, 38, 37, 36, 35, 34], 'temperature_2m_min' => [25, 29, 24, 23, 22, 21, 20], 'precipitation_sum' => [0, 0, 0, 0, 0, 0, 0]]])]);

        $forecast = app(OpenMeteoService::class)->forecast($quartier);
        $this->assertCount(7, $forecast['days']);
        $this->assertSame(42.0, $forecast['days'][1]['temperature_max']);
        app(OpenMeteoService::class)->forecast($quartier);
        Http::assertSentCount(1);
    }

    public function test_internal_weather_thresholds_are_centralized(): void
    {
        $risk = app(WeatherRiskService::class);
        $this->assertSame('Normal', $risk->level(34));
        $this->assertSame('Vigilance', $risk->level(35));
        $this->assertSame('Alerte', $risk->level(39));
        $this->assertSame('Critique', $risk->level(42));
    }

    public function test_critical_alert_notifies_only_residents_of_its_quartier_without_duplicates(): void
    {
        $bardo = Quartier::create(['nom' => 'Bardo', 'ville' => 'Tunis', 'latitude' => 36.8, 'longitude' => 10.1, 'actif' => true]);
        $other = Quartier::create(['nom' => 'Centre', 'ville' => 'Tunis', 'latitude' => 36.7, 'longitude' => 10.2, 'actif' => true]);
        $resident = User::factory()->create(['quartier_id' => $bardo->id]);
        $outsider = User::factory()->create(['quartier_id' => $other->id]);
        $vigilance = ['temperature' => 39.0, 'days' => [['temperature_max' => 39, 'temperature_min' => 25, 'niveau' => 'Alerte', 'date' => today()->toDateString()], ['temperature_max' => 42, 'temperature_min' => 29, 'niveau' => 'Critique', 'date' => today()->addDay()->toDateString()]]];

        $service = app(WeatherVigilanceService::class);
        $service->updateAlert($bardo, $vigilance);
        $service->updateAlert($bardo, $vigilance);
        $this->assertCount(1, $resident->fresh()->notifications);
        $this->assertCount(0, $outsider->fresh()->notifications);
        $this->assertDatabaseCount('alerte_meteos', 1);
    }

    public function test_notifications_are_private_and_admin_vigilance_is_protected(): void
    {
        $resident = User::factory()->create(['role' => 'habitant']);
        $admin = User::factory()->create(['role' => 'admin']);
        $resident->notify(new WeatherVigilanceNotification(AlerteMeteo::create(['quartier_id' => Quartier::create(['nom' => 'La Marsa', 'ville' => 'Tunis', 'latitude' => 36.8, 'longitude' => 10.2, 'actif' => true])->id, 'titre' => 'Alerte', 'niveau' => 'Critique', 'temperature_max' => 42, 'source' => 'open-meteo', 'actif' => true])));

        $this->actingAs($resident)->get(route('notifications.index'))->assertOk()->assertSee('Alerte');
        $this->actingAs($resident)->get(route('admin.vigilance-meteo.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.vigilance-meteo.index'))->assertOk();
    }
}
