<?php

namespace Tests\Feature;

use App\Models\CoupureElectrique;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoupureLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_saves_the_coordinates_of_the_selected_quartier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier();

        $this->actingAs($admin)->post(route('admin.coupures.store'), $this->payload($quartier, [
            'latitude' => 1,
            'longitude' => 2,
        ]))->assertRedirect(route('admin.coupures.index'));

        $this->assertDatabaseHas('coupure_electriques', [
            'titre' => 'Coupure localisée',
            'adresse' => 'Bardo, Tunis',
            'latitude' => $quartier->latitude,
            'longitude' => $quartier->longitude,
        ]);
    }

    public function test_admin_uses_quartier_coordinates_and_address_when_no_address_is_given(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier();

        $this->actingAs($admin)->post(route('admin.coupures.store'), $this->payload($quartier))
            ->assertRedirect(route('admin.coupures.index'));

        $coupure = CoupureElectrique::where('titre', 'Coupure localisée')->firstOrFail();
        $this->assertSame((float) $quartier->latitude, (float) $coupure->latitude);
        $this->assertSame((float) $quartier->longitude, (float) $coupure->longitude);
        $this->assertSame('Bardo, Tunis', $coupure->adresse);
    }

    private function quartier(): Quartier
    {
        return Quartier::create([
            'nom' => 'Bardo',
            'ville' => 'Tunis',
            'latitude' => 36.809,
            'longitude' => 10.14,
            'actif' => true,
        ]);
    }

    private function payload(Quartier $quartier, array $overrides = []): array
    {
        return array_merge([
            'quartier_id' => $quartier->id,
            'titre' => 'Coupure localisée',
            'type' => 'maintenance',
            'statut' => 'prévue',
            'actif' => true,
        ], $overrides);
    }
}
