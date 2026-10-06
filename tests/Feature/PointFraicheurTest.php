<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\PointFraicheur;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointFraicheurTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_points_fraicheur(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier('Bardo');
        $payload = $this->pointPayload($quartier);

        $this->actingAs($admin)->get(route('admin.points-fraicheur.index'))->assertOk()->assertSee('Points officiels HeatAlert');
        $this->actingAs($admin)->get(route('admin.points-fraicheur.geoapify'))->assertOk()->assertSee('Rechercher avec Geoapify');

        $this->actingAs($admin)->post(route('admin.points-fraicheur.store'), $payload)
            ->assertRedirect(route('admin.points-fraicheur.index'));
        $this->assertDatabaseHas('points_fraicheur', ['nom' => 'Parc Bardo', 'source' => 'manuel']);

        $point = PointFraicheur::firstOrFail();
        $this->actingAs($admin)->put(route('admin.points-fraicheur.update', $point), array_merge($payload, ['nom' => 'Parc Bardo modifié']))
            ->assertRedirect(route('admin.points-fraicheur.show', $point));
        $this->assertDatabaseHas('points_fraicheur', ['id' => $point->id, 'nom' => 'Parc Bardo modifié']);

        $this->actingAs($admin)->delete(route('admin.points-fraicheur.destroy', $point))
            ->assertRedirect(route('admin.points-fraicheur.index'));
        $this->assertDatabaseMissing('points_fraicheur', ['id' => $point->id]);
    }

    public function test_habitant_is_forbidden_from_admin_points_and_avis(): void
    {
        $habitant = User::factory()->create(['role' => 'habitant']);

        $this->actingAs($habitant)->get(route('admin.points-fraicheur.index'))->assertForbidden();
        $this->actingAs($habitant)->get(route('admin.avis.index'))->assertForbidden();
    }

    public function test_admin_can_import_geoapify_place_once_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quartier = $this->quartier('Bardo');
        $payload = [
            'quartier_id' => $quartier->id, 'place_id' => 'geo-place-1', 'nom' => 'Parc Geoapify',
            'adresse' => 'Bardo', 'latitude' => 36.809, 'longitude' => 10.14,
            'categories' => json_encode(['leisure.park']),
        ];

        $this->actingAs($admin)->post(route('admin.points-fraicheur.geoapify.import'), $payload)->assertRedirect();
        $this->assertDatabaseHas('points_fraicheur', ['geoapify_place_id' => 'geo-place-1', 'source' => 'geoapify', 'type' => 'Parc']);

        $this->actingAs($admin)->post(route('admin.points-fraicheur.geoapify.import'), $payload)->assertSessionHasErrors('geoapify');
        $this->assertSame(1, PointFraicheur::where('geoapify_place_id', 'geo-place-1')->count());
    }

    public function test_habitant_sees_only_active_points_of_own_quartier(): void
    {
        $bardo = $this->quartier('Bardo');
        $marsa = $this->quartier('Marsa');
        $user = $this->userInQuartier($bardo);
        $visible = PointFraicheur::create(array_merge($this->pointPayload($bardo), ['nom' => 'Point visible']));
        PointFraicheur::create(array_merge($this->pointPayload($bardo), ['nom' => 'Point inactif', 'actif' => false]));
        PointFraicheur::create(array_merge($this->pointPayload($marsa), ['nom' => 'Point autre quartier']));

        $this->actingAs($user)->get(route('points-fraicheur.index'))
            ->assertOk()->assertSee('Point visible')->assertDontSee('Point inactif')->assertDontSee('Point autre quartier');
        $this->actingAs($user)->get(route('points-fraicheur.show', $visible))->assertOk();
    }

    public function test_habitant_can_submit_one_pending_review_and_invalid_note_is_rejected(): void
    {
        $quartier = $this->quartier('Bardo');
        $user = $this->userInQuartier($quartier);
        $point = PointFraicheur::create($this->pointPayload($quartier));

        $this->actingAs($user)->post(route('points-fraicheur.avis.store', $point), ['note' => 6])->assertSessionHasErrors('note');
        $this->actingAs($user)->post(route('points-fraicheur.avis.store', $point), ['note' => 4, 'commentaire' => 'Endroit calme et agréable.'])->assertSessionHas('success');
        $this->assertDatabaseHas('avis', ['user_id' => $user->id, 'point_fraicheur_id' => $point->id, 'statut' => 'en_attente']);

        $this->actingAs($user)->post(route('points-fraicheur.avis.store', $point), ['note' => 4])->assertSessionHasErrors('avis');
        $this->assertSame(1, Avis::count());
    }

    public function test_user_cannot_update_or_delete_another_users_review(): void
    {
        $quartier = $this->quartier('Bardo');
        $owner = $this->userInQuartier($quartier);
        $other = $this->userInQuartier($quartier);
        $point = PointFraicheur::create($this->pointPayload($quartier));
        $avis = Avis::create(['user_id' => $owner->id, 'point_fraicheur_id' => $point->id, 'note' => 4, 'statut' => 'en_attente']);

        $this->actingAs($other)->put(route('points-fraicheur.avis.update', [$point, $avis]), ['note' => 5])->assertForbidden();
        $this->actingAs($other)->delete(route('points-fraicheur.avis.destroy', [$point, $avis]))->assertForbidden();
    }

    public function test_only_published_reviews_are_public_and_admin_can_publish_or_reject(): void
    {
        $quartier = $this->quartier('Bardo');
        $author = $this->userInQuartier($quartier);
        $viewer = $this->userInQuartier($quartier);
        $admin = User::factory()->create(['role' => 'admin']);
        $point = PointFraicheur::create($this->pointPayload($quartier));
        $pending = Avis::create(['user_id' => $author->id, 'point_fraicheur_id' => $point->id, 'note' => 1, 'commentaire' => 'Privé', 'statut' => 'en_attente']);

        $this->actingAs($viewer)->get(route('points-fraicheur.show', $point))->assertDontSee('Privé');
        $this->actingAs($admin)->get(route('admin.avis.show', $pending))->assertOk()->assertSee('Privé');
        $this->actingAs($admin)->put(route('admin.avis.publish', $pending))->assertSessionHas('success');
        $this->assertDatabaseHas('avis', ['id' => $pending->id, 'statut' => 'publie']);
        $this->actingAs($viewer)->get(route('points-fraicheur.show', $point))->assertSee('Privé')->assertSee('1,0 / 5');

        $second = Avis::create(['user_id' => $viewer->id, 'point_fraicheur_id' => $point->id, 'note' => 5, 'statut' => 'en_attente']);
        $this->actingAs($admin)->put(route('admin.avis.reject', $second))->assertSessionHas('success');
        $this->assertDatabaseHas('avis', ['id' => $second->id, 'statut' => 'rejete']);
    }

    public function test_new_module_views_keep_blade_inheritance_and_real_sidebar_routes(): void
    {
        $this->assertStringContainsString("@extends('layouts.user')", file_get_contents(resource_path('views/points-fraicheur/index.blade.php')));
        $this->assertStringContainsString("@extends('layouts.admin')", file_get_contents(resource_path('views/admin/points-fraicheur/index.blade.php')));
        $this->assertStringContainsString("route('points-fraicheur.index')", file_get_contents(resource_path('views/partials/user-sidebar.blade.php')));
        $this->assertStringContainsString("route('admin.points-fraicheur.index')", file_get_contents(resource_path('views/partials/admin-sidebar.blade.php')));
    }

    private function quartier(string $nom): Quartier
    {
        return Quartier::create(['nom' => $nom, 'ville' => 'Tunis', 'latitude' => 36.809, 'longitude' => 10.14, 'actif' => true]);
    }

    private function userInQuartier(Quartier $quartier): User
    {
        $user = User::factory()->create(['role' => 'habitant']);
        $user->quartier_id = $quartier->id;
        $user->save();

        return $user;
    }

    private function pointPayload(Quartier $quartier): array
    {
        return ['quartier_id' => $quartier->id, 'nom' => 'Parc Bardo', 'type' => 'Parc', 'adresse' => 'Avenue Habib Bourguiba', 'latitude' => 36.809, 'longitude' => 10.14, 'actif' => true];
    }
}
