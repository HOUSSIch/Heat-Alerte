<?php

namespace Tests\Feature;

use App\Models\CoupureElectrique;
use App\Models\Quartier;
use App\Models\User;
use App\Services\CoupureTrackingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MemberThreeFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_reports_scheduled_in_progress_overdue_and_finished(): void
    {
        $quartier = $this->quartier();
        $service = app(CoupureTrackingService::class);
        $now = Carbon::parse('2026-10-20 12:00:00');

        $this->assertSame('Programmée', $service->track($this->outage($quartier, $now->copy()->addHour(), $now->copy()->addHours(2)), $now)['status']);
        $active = $service->track($this->outage($quartier, $now->copy()->subHour(), $now->copy()->addHour()), $now);
        $this->assertSame('En cours', $active['status']);
        $this->assertSame(50, $active['progress']);
        $this->assertSame('En retard / prolongée', $service->track($this->outage($quartier, $now->copy()->subHours(2), $now->copy()->subHour()), $now)['status']);
        $this->assertSame('Terminée', $service->track($this->outage($quartier, $now->copy()->subHours(2), $now->copy()->subHour(), $now->copy()->subMinutes(30)), $now)['status']);
    }

    public function test_planning_is_scoped_to_resident_quartier_and_admin_sees_all(): void
    {
        $bardo = $this->quartier('Bardo');
        $other = $this->quartier('Lac');
        $resident = User::factory()->create(['quartier_id' => $bardo->id]);
        $admin = User::factory()->create(['role' => 'admin']);
        $local = $this->outage($bardo, now()->addDay(), now()->addDays(2));
        $remote = $this->outage($other, now()->addDay(), now()->addDays(2));

        $this->actingAs($resident)->get(route('coupures.planning'))->assertOk()->assertSee($local->titre)->assertDontSee($remote->titre);
        $this->actingAs($admin)->get(route('admin.coupures.planning'))->assertOk()->assertSee($local->titre)->assertSee($remote->titre);
    }

    public function test_authenticated_user_can_fill_signalement_with_validated_ai_json(): void
    {
        config(['services.groq.key' => 'test-key', 'services.groq.model' => 'openai/gpt-oss-20b']);
        $quartier = $this->quartier();
        $user = User::factory()->create(['quartier_id' => $quartier->id]);
        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response(['choices' => [['message' => ['content' => '{"titre":"Coupure avenue Habib Bourguiba","description":"Absence de courant depuis deux heures.","adresse":"Avenue Habib Bourguiba, Le Bardo","quartier":"Bardo"}']]]]),
        ]);

        $this->actingAs($user)->postJson(route('signalements.assistant-fill'), ['texte' => 'Plus de courant avenue Habib Bourguiba au Bardo'])
            ->assertOk()->assertJsonPath('titre', 'Coupure avenue Habib Bourguiba')->assertJsonPath('quartier_id', $quartier->id)->assertJsonPath('adresse', 'Avenue Habib Bourguiba, Le Bardo');
        Http::assertSent(fn ($request) => $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
            && $request['model'] === 'openai/gpt-oss-20b');
        Http::assertNotSent(fn ($request) => str_starts_with($request->url(), 'https://nominatim.openstreetmap.org'));
        $this->actingAsGuest()->postJson(route('signalements.assistant-fill'), ['texte' => 'test'])->assertUnauthorized();
    }

    public function test_signalement_assistant_uses_the_user_quartier_when_groq_returns_an_unknown_quartier(): void
    {
        config(['services.groq.key' => 'test-key']);
        $quartier = $this->quartier('Bardo');
        $user = User::factory()->create(['quartier_id' => $quartier->id]);

        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response(['choices' => [['message' => ['content' => '{"titre":"Coupure électrique","description":"Le quartier ne dispose plus d’électricité depuis ce matin.","adresse":null,"quartier":"Quartier imaginaire"}']]]]),
        ]);

        $this->actingAs($user)
            ->postJson(route('signalements.assistant-fill'), ['texte' => 'Il y a une coupure grave à Manar C, le petit Mall Manar City.'])
            ->assertOk()
            ->assertJsonPath('quartier_id', $quartier->id)
            ->assertJsonPath('adresse', null);

        $this->assertDatabaseCount('quartiers', 1);
    }

    public function test_signalement_assistant_returns_a_clear_error_when_groq_reaches_its_quota(): void
    {
        config(['services.groq.key' => 'test-key']);
        $user = User::factory()->create(['quartier_id' => $this->quartier()->id]);

        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([], 429),
        ]);

        $this->actingAs($user)
            ->postJson(route('signalements.assistant-fill'), ['texte' => 'Coupure électrique dans ma rue.'])
            ->assertStatus(429)
            ->assertJsonPath('message', 'Le quota du service IA de saisie est temporairement atteint.');
    }

    public function test_signalement_assistant_rejects_an_invalid_groq_json_response(): void
    {
        config(['services.groq.key' => 'test-key']);
        $user = User::factory()->create(['quartier_id' => $this->quartier()->id]);

        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response(['choices' => [['message' => ['content' => 'réponse non structurée']]]]),
        ]);

        $this->actingAs($user)
            ->postJson(route('signalements.assistant-fill'), ['texte' => 'Coupure électrique dans ma rue.'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Impossible d’interpréter la réponse du service IA.');
    }

    public function test_signalement_assistant_stops_cleanly_when_groq_times_out(): void
    {
        config(['services.groq.key' => 'test-key']);
        $user = User::factory()->create(['quartier_id' => $this->quartier()->id]);

        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => function () {
                throw new ConnectionException('cURL error 28: Operation timed out');
            },
        ]);

        $this->actingAs($user)
            ->postJson(route('signalements.assistant-fill'), ['texte' => 'Coupure électrique dans ma rue.'])
            ->assertStatus(504)
            ->assertJsonPath('message', 'Le service IA met trop de temps à répondre. Veuillez réessayer.');
    }

    public function test_signalement_form_includes_a_csrf_meta_tag_and_safe_token_fallback(): void
    {
        $user = User::factory()->create(['quartier_id' => $this->quartier()->id]);

        $this->actingAs($user)
            ->get(route('signalements.create'))
            ->assertOk()
            ->assertSee('name="csrf-token"', false)
            ->assertSee('csrfMeta?.getAttribute', false)
            ->assertSee('input[name="_token"]', false);
    }

    private function quartier(string $nom = 'Bardo'): Quartier
    {
        return Quartier::create(['nom' => $nom, 'ville' => 'Tunis', 'latitude' => 36.8, 'longitude' => 10.1, 'actif' => true]);
    }

    private function outage(Quartier $quartier, Carbon $start, Carbon $end, ?Carbon $actual = null): CoupureElectrique
    {
        return CoupureElectrique::create(['quartier_id' => $quartier->id, 'titre' => 'Coupure '.$quartier->nom.' '.$start->timestamp, 'type' => 'maintenance', 'statut' => 'prévue', 'actif' => true, 'date_debut' => $start, 'date_fin_estimee' => $end, 'date_fin_reelle' => $actual]);
    }
}
