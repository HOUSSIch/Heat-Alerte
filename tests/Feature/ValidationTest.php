<?php

namespace Tests\Feature;

use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_rejects_invalid_required_fields(): void
    {
        $this->post('/register', [
            'name' => '',
            'email' => 'adresse-invalide',
            'password' => 'court',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_admin_quartier_form_validates_coordinates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.quartiers.store'), [
            'nom' => 'Bardo',
            'ville' => 'Tunis',
            'latitude' => 91,
            'longitude' => 181,
        ])->assertSessionHasErrors(['latitude', 'longitude']);

        $this->assertDatabaseCount('quartiers', 0);
    }

    public function test_admin_meteo_form_validates_temperature_range_and_level(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = Quartier::create([
            'nom' => 'Bardo',
            'ville' => 'Tunis',
            'latitude' => 36.809,
            'longitude' => 10.14,
            'actif' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.alertes-meteo.store'), [
            'quartier_id' => $quartier->id,
            'titre' => 'Alerte de test',
            'temperature_min' => 40,
            'temperature_max' => 30,
            'niveau' => 'Invalide',
        ])->assertSessionHasErrors(['temperature_min', 'temperature_max', 'niveau']);

        $this->assertDatabaseCount('alerte_meteos', 0);
    }

    public function test_admin_point_fraicheur_form_validates_related_quartier_and_coordinates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.points-fraicheur.store'), [
            'quartier_id' => 99999,
            'nom' => 'Parc',
            'type' => 'Parc',
            'adresse' => 'Tunis',
            'latitude' => 100,
            'longitude' => 200,
        ])->assertSessionHasErrors(['quartier_id', 'latitude', 'longitude']);

        $this->assertDatabaseCount('points_fraicheur', 0);
    }
}
