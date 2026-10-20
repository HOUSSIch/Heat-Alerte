<?php

namespace Tests\Feature;

use App\Models\PublicationQuartier;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ValidationHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_equipment_validation_rejects_short_names_and_oversized_uploads(): void
    {
        $user = User::factory()->create();
        $base = ['type' => 'Climatisation'];

        $this->actingAs($user)->post(route('equipements.store'), $base + ['nom' => 'A'])
            ->assertSessionHasErrors('nom');
        $this->actingAs($user)->post(route('equipements.store'), $base + ['nom' => 'Climatiseur', 'photo' => UploadedFile::fake()->image('too-large.jpg')->size(4097)])
            ->assertSessionHasErrors('photo');
    }

    public function test_profile_does_not_allow_role_injection_and_validates_active_quartier_existence(): void
    {
        $user = User::factory()->create(['role' => 'habitant']);
        $this->actingAs($user)->put(route('profile.update'), ['name' => 'Utilisateur sûr', 'email' => 'safe@example.test', 'role' => 'admin', 'quartier_id' => 999])
            ->assertSessionHasErrors('quartier_id');
        $this->assertSame('habitant', $user->fresh()->role);
    }

    public function test_signalement_uses_the_authenticated_users_quartier_not_the_posted_one(): void
    {
        $own = $this->quartier('Bardo');
        $other = $this->quartier('Marsa');
        $user = User::factory()->create(['quartier_id' => $own->id]);

        $this->actingAs($user)->post(route('signalements.store'), [
            'quartier_id' => $other->id,
            'titre' => 'Coupure électrique dans la rue',
            'description' => 'Le courant est absent depuis le début de l’après-midi.',
        ])->assertRedirect();

        $this->assertDatabaseHas('signalements', ['user_id' => $user->id, 'quartier_id' => $own->id]);
    }

    public function test_forum_and_comment_reject_blank_short_and_protected_values(): void
    {
        $quartier = $this->quartier('Bardo');
        $user = User::factory()->create(['quartier_id' => $quartier->id]);

        $this->actingAs($user)->post(route('discussions-quartier.store'), ['titre' => '   ', 'contenu' => 'court', 'type' => 'hacked', 'statut' => 'resolu'])
            ->assertSessionHasErrors(['titre', 'contenu', 'type']);

        $publication = PublicationQuartier::create(['user_id' => $user->id, 'quartier_id' => $quartier->id, 'titre' => 'Discussion sécurisée', 'contenu' => 'Contenu suffisamment détaillé pour la publication.', 'type' => 'probleme', 'statut' => 'signale']);
        $this->actingAs($user)->post(route('discussions-quartier.comment', $publication), ['contenu' => ' '])->assertSessionHasErrors('contenu');
        $this->assertSame('signale', $publication->fresh()->statut);
    }

    private function quartier(string $nom): Quartier
    {
        return Quartier::create(['nom' => $nom, 'ville' => 'Tunis', 'latitude' => 36.8, 'longitude' => 10.1, 'actif' => true]);
    }
}
