<?php

namespace Tests\Feature;

use App\Models\CoupureElectrique;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoupureDatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_an_outage_with_start_and_estimated_end_dates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier();

        $this->actingAs($admin)
            ->get(route('admin.coupures.create'))
            ->assertOk()
            ->assertSee('name="date_debut"', false)
            ->assertSee('name="date_fin_estimee"', false)
            ->assertSee('name="date_fin_reelle"', false);

        $this->actingAs($admin)
            ->post(route('admin.coupures.store'), $this->payload($quartier, [
                'date_debut' => '2026-10-18T18:30',
                'date_fin_estimee' => '2026-10-18T21:30',
            ]))
            ->assertRedirect(route('admin.coupures.index'));

        $coupure = CoupureElectrique::firstOrFail();

        $this->assertSame('2026-10-18 18:30:00', $coupure->date_debut?->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-18 21:30:00', $coupure->date_fin_estimee?->format('Y-m-d H:i:s'));
        $this->assertNull($coupure->date_fin_reelle);
    }

    public function test_admin_can_add_the_actual_end_date_when_updating_an_outage(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier();
        $coupure = CoupureElectrique::create($this->payload($quartier, [
            'date_debut' => '2026-10-18 18:30:00',
            'date_fin_estimee' => '2026-10-18 21:30:00',
        ]));

        $this->actingAs($admin)
            ->put(route('admin.coupures.update', $coupure), $this->payload($quartier, [
                'date_debut' => '2026-10-18T18:30',
                'date_fin_estimee' => '2026-10-18T21:30',
                'date_fin_reelle' => '2026-10-18T20:45',
            ]))
            ->assertRedirect(route('admin.coupures.show', $coupure));

        $this->assertSame('2026-10-18 20:45:00', $coupure->fresh()->date_fin_reelle?->format('Y-m-d H:i:s'));
    }

    public function test_admin_cannot_save_an_end_date_before_the_start_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier();

        $this->actingAs($admin)
            ->from(route('admin.coupures.create'))
            ->post(route('admin.coupures.store'), $this->payload($quartier, [
                'date_debut' => '2026-10-18T18:30',
                'date_fin_estimee' => '2026-10-18T18:00',
                'date_fin_reelle' => '2026-10-18T18:15',
            ]))
            ->assertRedirect(route('admin.coupures.create'))
            ->assertSessionHasErrors(['date_fin_estimee', 'date_fin_reelle']);
    }

    public function test_front_office_displays_only_the_planning_dates_that_exist(): void
    {
        $quartier = $this->quartier();
        $resident = User::factory()->create(['quartier_id' => $quartier->id]);
        $coupure = CoupureElectrique::create($this->payload($quartier, [
            'date_debut' => '2026-10-18 18:30:00',
            'date_fin_estimee' => '2026-10-18 21:30:00',
            'date_fin_reelle' => '2026-10-18 20:45:00',
        ]));

        $this->actingAs($resident)
            ->get(route('coupures.show', $coupure))
            ->assertOk()
            ->assertSee('Planning')
            ->assertSee('18/10/2026 à 18:30')
            ->assertSee('18/10/2026 à 21:30')
            ->assertSee('Fin réelle')
            ->assertSee('18/10/2026 à 20:45')
            ->assertDontSee('À confirmer');
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
            'titre' => 'Coupure planifiée',
            'description' => 'Intervention électrique programmée.',
            'type' => 'maintenance',
            'statut' => 'prévue',
            'actif' => true,
        ], $overrides);
    }
}
