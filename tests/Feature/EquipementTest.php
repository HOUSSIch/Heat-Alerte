<?php

namespace Tests\Feature;

use App\Models\Equipement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/equipements')->assertRedirect('/login');
        $this->get('/equipements/create')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_the_empty_list_and_create_an_equipement(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/equipements')->assertOk()->assertSee('Aucun équipement enregistré');

        $this->actingAs($user)->post('/equipements', [
            'nom' => 'Réfrigérateur',
            'type' => 'Électroménager',
            'marque' => 'HeatCool',
            'sensible_chaleur' => true,
            'sensible_coupure' => true,
            'description' => 'Conserver les aliments au frais.',
            'user_id' => 999,
        ])->assertRedirect('/equipements');

        $this->assertDatabaseHas('equipements', [
            'user_id' => $user->id,
            'nom' => 'Réfrigérateur',
            'sensible_chaleur' => true,
            'sensible_coupure' => true,
        ]);
    }

    public function test_owner_can_show_update_and_delete_an_equipement(): void
    {
        $user = User::factory()->create();
        $equipement = $user->equipements()->create([
            'nom' => 'Ordinateur', 'type' => 'Informatique', 'sensible_chaleur' => true, 'sensible_coupure' => false,
        ]);

        $this->actingAs($user)->get(route('equipements.show', $equipement))->assertOk()->assertSee('Ordinateur');
        $this->actingAs($user)->get(route('equipements.edit', $equipement))->assertOk()->assertSee('Enregistrer les modifications');

        $this->actingAs($user)->put(route('equipements.update', $equipement), [
            'nom' => 'Ordinateur portable', 'type' => 'Informatique', 'marque' => 'Tabler',
            'sensible_chaleur' => false, 'sensible_coupure' => true, 'description' => 'Sauvegarder le travail.',
        ])->assertRedirect(route('equipements.show', $equipement));

        $this->assertDatabaseHas('equipements', ['id' => $equipement->id, 'nom' => 'Ordinateur portable', 'sensible_coupure' => true]);

        $this->actingAs($user)->delete(route('equipements.destroy', $equipement))->assertRedirect('/equipements');
        $this->assertDatabaseMissing('equipements', ['id' => $equipement->id]);
    }

    public function test_user_cannot_access_another_users_equipement(): void
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $equipement = $owner->equipements()->create([
            'nom' => 'Appareil médical', 'type' => 'Médical', 'sensible_chaleur' => true, 'sensible_coupure' => true,
        ]);

        $this->actingAs($visitor)->get(route('equipements.show', $equipement))->assertNotFound();
        $this->actingAs($visitor)->get(route('equipements.edit', $equipement))->assertNotFound();
        $this->actingAs($visitor)->put(route('equipements.update', $equipement), ['nom' => 'Intrusion', 'type' => 'Autre'])->assertNotFound();
        $this->actingAs($visitor)->delete(route('equipements.destroy', $equipement))->assertNotFound();

        $this->assertDatabaseHas('equipements', ['id' => $equipement->id, 'user_id' => $owner->id]);
    }

    public function test_dashboard_displays_the_authenticated_users_equipement_count(): void
    {
        $user = User::factory()->create();
        $user->equipements()->create(['nom' => 'Climatiseur', 'type' => 'Climatisation', 'sensible_chaleur' => true, 'sensible_coupure' => false]);

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Mes équipements')->assertSee('>1<', false);
    }
}
