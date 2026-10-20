<?php

namespace Tests\Feature;

use App\Models\EtablissementSensible;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtablissementSensibleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_create_update_and_delete_an_etablissement_sensible(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier('El Menzah');
        $payload = $this->payload($quartier);

        $this->actingAs($admin)
            ->get(route('admin.etablissements-sensibles.index'))
            ->assertOk()
            ->assertSee('Établissements sensibles');

        $this->actingAs($admin)
            ->post(route('admin.etablissements-sensibles.store'), $payload)
            ->assertRedirect(route('admin.etablissements-sensibles.index'));

        $this->assertDatabaseHas('etablissement_sensibles', [
            'nom' => 'Maison de retraite El Menzah',
            'quartier_id' => $quartier->id,
            'horaires' => 'Lun–Ven : 08:00–17:00',
            'services' => 'Information et orientation.',
            'accueil_public' => true,
            'actif' => true,
        ]);

        $etablissement = EtablissementSensible::firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.etablissements-sensibles.update', $etablissement), [
                ...$payload,
                'nom' => 'Maison de retraite El Menzah modifiée',
                'horaires' => 'Tous les jours : 09:00–18:00',
                'services' => 'Accueil et accompagnement.',
                'accueil_public' => false,
                'actif' => false,
            ])
            ->assertRedirect(route('admin.etablissements-sensibles.show', $etablissement));

        $this->assertDatabaseHas('etablissement_sensibles', [
            'id' => $etablissement->id,
            'nom' => 'Maison de retraite El Menzah modifiée',
            'horaires' => 'Tous les jours : 09:00–18:00',
            'services' => 'Accueil et accompagnement.',
            'accueil_public' => false,
            'actif' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.etablissements-sensibles.destroy', $etablissement))
            ->assertRedirect(route('admin.etablissements-sensibles.index'));

        $this->assertDatabaseMissing('etablissement_sensibles', ['id' => $etablissement->id]);
    }

    public function test_habitant_cannot_access_admin_etablissements_routes(): void
    {
        $habitant = User::factory()->create(['role' => 'habitant']);
        $etablissement = $this->etablissement($this->quartier('El Menzah'));

        $this->actingAs($habitant)->get(route('admin.etablissements-sensibles.index'))->assertForbidden();
        $this->actingAs($habitant)->get(route('admin.etablissements-sensibles.create'))->assertForbidden();
        $this->actingAs($habitant)->post(route('admin.etablissements-sensibles.store'), $this->payload($etablissement->quartier))->assertForbidden();
        $this->actingAs($habitant)->get(route('admin.etablissements-sensibles.edit', $etablissement))->assertForbidden();
    }

    public function test_habitant_only_sees_active_etablissements_in_own_quartier(): void
    {
        $elMenzah = $this->quartier('El Menzah');
        $bardo = $this->quartier('Bardo');
        $user = User::factory()->create(['role' => 'habitant', 'quartier_id' => $elMenzah->id]);

        $visible = $this->etablissement($elMenzah, [
            'nom' => 'Visible El Menzah',
            'accueil_public' => true,
            'horaires' => '08:00–17:00',
            'services' => 'Accueil et orientation.',
        ]);
        $this->etablissement($elMenzah, ['nom' => 'Inactif El Menzah', 'actif' => false]);
        $otherQuartier = $this->etablissement($bardo, ['nom' => 'Autre quartier']);

        $this->actingAs($user)
            ->get(route('etablissements-sensibles.index'))
            ->assertOk()
            ->assertSee('Visible El Menzah')
            ->assertDontSee('Inactif El Menzah')
            ->assertDontSee('Autre quartier');

        $this->actingAs($user)
            ->get(route('etablissements-sensibles.index'))
            ->assertSee('Structures d’assistance')
            ->assertSee('Accueil du public : Oui')
            ->assertSee('08:00–17:00');

        $this->actingAs($user)
            ->get(route('etablissements-sensibles.show', $visible))
            ->assertOk()
            ->assertSee('Vous pouvez contacter ou vous rendre à cette structure si nécessaire.')
            ->assertSee('Accueil et orientation.');
        $this->actingAs($user)->get(route('etablissements-sensibles.show', $otherQuartier))->assertNotFound();
    }

    public function test_habitant_without_quartier_sees_a_clear_empty_page(): void
    {
        $user = User::factory()->create(['role' => 'habitant', 'quartier_id' => null]);

        $this->actingAs($user)
            ->get(route('etablissements-sensibles.index'))
            ->assertOk()
            ->assertSee('Veuillez sélectionner votre quartier');
    }

    public function test_admin_validation_rejects_missing_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.etablissements-sensibles.store'), [])
            ->assertSessionHasErrors(['quartier_id', 'nom', 'type', 'adresse']);

        $this->assertDatabaseCount('etablissement_sensibles', 0);
    }

    private function quartier(string $nom): Quartier
    {
        return Quartier::create([
            'nom' => $nom,
            'ville' => 'Tunis',
            'latitude' => 36.809,
            'longitude' => 10.14,
            'actif' => true,
        ]);
    }

    private function etablissement(Quartier $quartier, array $attributes = []): EtablissementSensible
    {
        return EtablissementSensible::create([
            ...$this->payload($quartier),
            ...$attributes,
        ]);
    }

    private function payload(Quartier $quartier): array
    {
        return [
            'quartier_id' => $quartier->id,
            'nom' => 'Maison de retraite El Menzah',
            'type' => 'Maison de retraite',
            'adresse' => 'Avenue de l’Indépendance',
            'telephone' => '71 000 000',
            'description' => 'Structure accueillant des personnes âgées.',
            'horaires' => 'Lun–Ven : 08:00–17:00',
            'services' => 'Information et orientation.',
            'accueil_public' => true,
            'actif' => true,
        ];
    }
}
