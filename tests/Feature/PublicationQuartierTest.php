<?php

namespace Tests\Feature;

use App\Models\PublicationQuartier;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicationQuartierTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_sees_only_own_quartier_and_cannot_open_another_publication(): void
    {
        [$bardo, $marsa] = [$this->quartier('Bardo'), $this->quartier('Marsa')];
        $resident = $this->resident($bardo);
        $local = $this->publication($resident, $bardo, 'Discussion Bardo');
        $remote = $this->publication($this->resident($marsa), $marsa, 'Discussion Marsa');

        $this->actingAs($resident)->get(route('discussions-quartier.index'))->assertOk()->assertSee($local->titre)->assertDontSee($remote->titre);
        $this->actingAs($resident)->get(route('discussions-quartier.show', $remote))->assertForbidden();
    }

    public function test_creation_ignores_a_forced_quartier_id(): void
    {
        $bardo = $this->quartier('Bardo');
        $marsa = $this->quartier('Marsa');
        $resident = $this->resident($bardo);

        $this->actingAs($resident)->post(route('discussions-quartier.store'), ['titre' => 'Information locale', 'type' => 'information', 'contenu' => 'Message utile.', 'quartier_id' => $marsa->id])->assertRedirect();
        $this->assertDatabaseHas('publications_quartier', ['titre' => 'Information locale', 'quartier_id' => $bardo->id, 'user_id' => $resident->id]);
    }

    public function test_comments_confirmations_and_status_are_scoped_and_protected(): void
    {
        $bardo = $this->quartier('Bardo');
        $marsa = $this->quartier('Marsa');
        $owner = $this->resident($bardo);
        $neighbor = $this->resident($bardo);
        $remote = $this->publication($this->resident($marsa), $marsa, 'Remote');
        $publication = $this->publication($owner, $bardo, 'Local');

        $this->actingAs($neighbor)->post(route('discussions-quartier.comment', $publication), ['contenu' => 'Je confirme également.'])->assertSessionHas('success');
        $this->assertDatabaseHas('commentaires_publication', ['publication_id' => $publication->id, 'user_id' => $neighbor->id]);
        $this->actingAs($neighbor)->post(route('discussions-quartier.confirmation', $publication))->assertSessionHas('success');
        $this->actingAs($neighbor)->post(route('discussions-quartier.confirmation', $publication))->assertSessionHas('success');
        $this->assertDatabaseCount('publication_confirmations', 0);
        $this->actingAs($neighbor)->patch(route('discussions-quartier.status', $publication), ['statut' => 'resolu'])->assertForbidden();
        $this->actingAs($owner)->patch(route('discussions-quartier.status', $publication), ['statut' => 'resolu'])->assertSessionHas('success');
        $this->assertDatabaseHas('publications_quartier', ['id' => $publication->id, 'statut' => 'resolu']);
        $this->actingAs($neighbor)->post(route('discussions-quartier.comment', $remote), ['contenu' => 'Interdit'])->assertForbidden();
    }

    public function test_admin_can_moderate_and_resident_is_forbidden_from_admin_forum(): void
    {
        $quartier = $this->quartier('Bardo');
        $resident = $this->resident($quartier);
        $publication = $this->publication($resident, $quartier, 'À modérer');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($resident)->get(route('admin.discussions-quartier.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.discussions-quartier.index'))->assertOk()->assertSee($publication->titre);
        $this->actingAs($admin)->put(route('admin.discussions-quartier.update', $publication), ['statut' => 'en_cours'])->assertSessionHas('success');
        $this->assertDatabaseHas('publications_quartier', ['id' => $publication->id, 'statut' => 'en_cours']);
    }

    public function test_summary_requires_auth_and_only_sends_the_requested_discussion(): void
    {
        config(['services.groq.key' => 'test-key']);
        $quartier = $this->quartier('Bardo');
        $owner = $this->resident($quartier);
        $publication = $this->publication($owner, $quartier, 'Coupure locale');
        $publication->commentaires()->createMany([['user_id' => $owner->id, 'contenu' => 'Toujours coupé chez moi.'], ['user_id' => $this->resident($quartier)->id, 'contenu' => 'Même problème dans notre rue.']]);
        $other = $this->publication($owner, $quartier, 'Discussion privée différente');
        $other->commentaires()->create(['user_id' => $owner->id, 'contenu' => 'Ne jamais envoyer ce commentaire.']);
        Http::fake(['https://api.groq.com/openai/v1/chat/completions' => Http::response(['choices' => [['message' => ['content' => '{"resume":"Plusieurs habitants signalent une coupure.","probleme_principal":"Coupure électrique","situation":"En cours","points_cles":["Deux habitants concernés"]}']]]])]);

        $this->postJson(route('discussions-quartier.resume-ia', $publication))->assertUnauthorized();
        $this->actingAs($owner)->postJson(route('discussions-quartier.resume-ia', $publication))->assertOk()->assertJsonPath('summary.probleme_principal', 'Coupure électrique');
        Http::assertSent(function ($request): bool {
            $content = $request['messages'][0]['content'];

            return str_contains($content, 'Coupure locale') && ! str_contains($content, 'Ne jamais envoyer ce commentaire.') && ! str_contains($content, $this->ownerEmailPlaceholder());
        });
    }

    public function test_summary_handles_groq_quota_timeout_and_invalid_json(): void
    {
        config(['services.groq.key' => 'test-key']);
        $quartier = $this->quartier('Bardo');
        $user = $this->resident($quartier);
        $publication = $this->publication($user, $quartier, 'Discussion IA');
        $publication->commentaires()->createMany([['user_id' => $user->id, 'contenu' => 'Premier commentaire.'], ['user_id' => $this->resident($quartier)->id, 'contenu' => 'Deuxième commentaire.']]);
        $url = route('discussions-quartier.resume-ia', $publication);

        $scenario = 'quota';
        Http::fake(function () use (&$scenario) {
            return match ($scenario) {
                'quota' => Http::response([], 429),
                'invalid' => Http::response(['choices' => [['message' => ['content' => 'texte non JSON']]]]),
                'timeout' => throw new ConnectionException('Operation timed out'),
            };
        });
        $this->actingAs($user)->postJson($url)->assertStatus(429)->assertJsonPath('message', 'Le quota du service de résumé IA est temporairement atteint. Veuillez réessayer plus tard.');

        Cache::flush();
        $scenario = 'invalid';
        $this->actingAs($user)->postJson($url)->assertStatus(422)->assertJsonPath('message', 'Impossible d’interpréter la réponse du service IA.');

        Cache::flush();
        $scenario = 'timeout';
        $this->actingAs($user)->postJson($url)->assertStatus(422)->assertJsonPath('message', 'Le service IA met trop de temps à répondre. Veuillez réessayer.');
    }

    private function quartier(string $nom): Quartier
    {
        return Quartier::create(['nom' => $nom, 'ville' => 'Tunis', 'latitude' => 36.8, 'longitude' => 10.1, 'actif' => true]);
    }

    private function resident(Quartier $quartier): User
    {
        return User::factory()->create(['role' => 'habitant', 'quartier_id' => $quartier->id]);
    }

    private function publication(User $user, Quartier $quartier, string $title): PublicationQuartier
    {
        return PublicationQuartier::create(['user_id' => $user->id, 'quartier_id' => $quartier->id, 'titre' => $title, 'contenu' => 'Contenu de '.$title, 'type' => 'probleme', 'statut' => 'signale']);
    }

    private function ownerEmailPlaceholder(): string
    {
        return '@example.com';
    }
}
