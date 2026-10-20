<?php

namespace Tests\Feature;

use App\Models\CoupureElectrique;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoupureQuartierTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_saves_and_updates_the_selected_quartier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tunis = $this->quartier('Tunis');
        $marsa = $this->quartier('La Marsa');

        $this->actingAs($admin)->post(route('admin.coupures.store'), $this->payload($tunis))
            ->assertRedirect(route('admin.coupures.index'));

        $coupure = CoupureElectrique::firstOrFail();
        $this->assertSame($tunis->id, $coupure->quartier_id);
        $this->assertTrue($tunis->coupures()->whereKey($coupure)->exists());

        $this->actingAs($admin)->put(route('admin.coupures.update', $coupure), $this->payload($marsa, 'Coupure déplacée'))
            ->assertRedirect(route('admin.coupures.show', $coupure));

        $this->assertDatabaseHas('coupure_electriques', ['id' => $coupure->id, 'quartier_id' => $marsa->id]);
    }

    public function test_resident_sees_only_active_coupures_from_own_quartier(): void
    {
        $tunis = $this->quartier('Tunis');
        $marsa = $this->quartier('La Marsa');
        $resident = User::factory()->create(['quartier_id' => $tunis->id]);
        $own = CoupureElectrique::create($this->payload($tunis, 'Coupure Tunis'));
        $other = CoupureElectrique::create($this->payload($marsa, 'Coupure Marsa'));

        $this->actingAs($resident)->get(route('coupures.index'))
            ->assertOk()->assertSee('Coupure Tunis')->assertDontSee('Coupure Marsa');
        $this->actingAs($resident)->get(route('coupures.show', $own))->assertOk();
        $this->actingAs($resident)->get(route('coupures.show', $other))->assertNotFound();
    }

    public function test_resident_without_quartier_sees_an_empty_local_list(): void
    {
        $quartier = $this->quartier('Tunis');
        CoupureElectrique::create($this->payload($quartier, 'Coupure locale'));
        $resident = User::factory()->create(['quartier_id' => null]);

        $this->actingAs($resident)->get(route('coupures.index'))
            ->assertOk()->assertSee('Votre profil n’est associé à aucun quartier')->assertDontSee('Coupure locale');
    }

    public function test_admin_can_assign_a_user_to_a_quartier_and_the_outage_is_then_visible(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier('Ben Arous');
        $resident = User::factory()->create(['quartier_id' => null]);
        $coupure = CoupureElectrique::create($this->payload($quartier, 'Coupure visible'));

        $this->actingAs($admin)->put(route('admin.users.update', $resident), [
            'name' => $resident->name,
            'email' => $resident->email,
            'role' => 'habitant',
            'quartier_id' => $quartier->id,
        ])->assertRedirect(route('admin.users.show', $resident));

        $this->assertSame($quartier->id, $resident->fresh()->quartier_id);
        $this->actingAs($resident)->get(route('coupures.index'))->assertOk()->assertSee($coupure->titre);
    }

    private function quartier(string $nom): Quartier
    {
        return Quartier::create(['nom' => $nom, 'ville' => 'Tunis', 'latitude' => 36.8, 'longitude' => 10.1, 'actif' => true]);
    }

    private function payload(Quartier $quartier, string $titre = 'Coupure test'): array
    {
        return ['quartier_id' => $quartier->id, 'titre' => $titre, 'description' => 'Intervention programmée', 'type' => 'maintenance', 'statut' => 'prévue', 'actif' => true];
    }
}
