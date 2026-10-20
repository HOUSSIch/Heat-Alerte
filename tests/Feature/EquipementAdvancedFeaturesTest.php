<?php

namespace Tests\Feature;

use App\Models\EntretienEquipement;
use App\Models\Equipement;
use App\Models\User;
use App\Services\EquipementQrService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EquipementAdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_render_and_download_a_private_qr_code(): void
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $equipement = $this->equipementFor($owner);

        $this->actingAs($owner)->get(route('equipements.qr', $equipement))
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml')
            ->assertSee('<svg', false);
        $this->actingAs($owner)->get(route('equipements.qr.download', $equipement))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename="equipement-'.$equipement->id.'-qr.svg"');
        $this->actingAs($visitor)->get(route('equipements.qr', $equipement))->assertNotFound();
    }

    public function test_qr_passport_contains_only_useful_equipment_data(): void
    {
        $owner = User::factory()->create(['email' => 'private@example.test']);
        $equipement = $this->equipementFor($owner, 'Climatiseur du salon');
        $equipement->conseils()->create([
            'titre' => 'Nettoyer les filtres', 'description' => 'Conseil de démonstration.', 'categorie' => 'Prévention', 'niveau' => 'Important', 'actif' => true,
        ]);
        $equipement->entretiens()->create([
            'titre' => 'Contrôle avant saison chaude', 'type' => 'Contrôle', 'date_prevue' => now()->addDays(5), 'rappel_avant_jours' => 3,
        ]);

        $payload = app(EquipementQrService::class)->buildPayload($equipement->fresh(['conseils', 'entretiens']));

        $this->assertStringContainsString('HEATALERT - PASSEPORT EQUIPEMENT', $payload);
        $this->assertStringContainsString('EQ-'.str_pad((string) $equipement->id, 4, '0', STR_PAD_LEFT), $payload);
        $this->assertStringContainsString('Climatiseur du salon', $payload);
        $this->assertStringContainsString('Type : Climatisation', $payload);
        $this->assertStringContainsString('Marque : LG', $payload);
        $this->assertStringContainsString('Sensible à la chaleur', $payload);
        $this->assertStringContainsString('Sensible aux coupures', $payload);
        $this->assertStringContainsString('État entretien : Prévu', $payload);
        $this->assertStringContainsString('Conseil principal : Nettoyer les filtres', $payload);
        $this->assertStringNotContainsString($owner->email, $payload);
        $this->assertStringNotContainsString('password', strtolower($payload));
        $this->assertStringNotContainsString('127.0.0.1', $payload);
        $this->assertStringNotContainsString('localhost', $payload);
        $this->assertStringNotContainsString('http://', $payload);
        $this->assertStringNotContainsString('https://', $payload);
        $this->assertStringNotContainsString('/equipements/', $payload);
    }

    public function test_qr_modal_stays_on_the_equipment_index_page(): void
    {
        $owner = User::factory()->create();
        $equipement = $this->equipementFor($owner);

        $this->actingAs($owner)->get(route('equipements.index'))
            ->assertOk()
            ->assertSee('data-bs-toggle="modal"', false)
            ->assertSee('qr-modal-'.$equipement->id)
            ->assertSee(route('equipements.pdf.download', $equipement));
    }

    public function test_owner_can_download_a_private_equipment_pdf(): void
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $equipement = $this->equipementFor($owner, 'Réfrigérateur cuisine');

        $this->actingAs($owner)->get(route('equipements.pdf.download', $equipement))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=fiche-EQ-'.str_pad((string) $equipement->id, 4, '0', STR_PAD_LEFT).'.pdf');
        $this->actingAs($visitor)->get(route('equipements.pdf.download', $equipement))->assertNotFound();
    }

    public function test_owner_can_manage_maintenance_and_overdue_status_is_calculated(): void
    {
        $owner = User::factory()->create();
        $equipement = $this->equipementFor($owner);

        $this->actingAs($owner)->post(route('equipements.entretiens.store', $equipement), [
            'titre' => 'Nettoyage filtre',
            'type' => 'Nettoyage',
            'date_prevue' => now()->subDay()->toDateString(),
            'rappel_avant_jours' => 3,
            'description' => 'Nettoyer le filtre.',
        ])->assertRedirect(route('equipements.show', $equipement));

        $entretien = EntretienEquipement::firstOrFail();
        $this->assertSame('en_retard', $entretien->statut());
        $this->actingAs($owner)->patch(route('equipements.entretiens.realiser', [$equipement, $entretien]))
            ->assertRedirect(route('equipements.show', $equipement));
        $this->assertSame('réalisé', $entretien->fresh()->statut());
        $this->actingAs($owner)->get(route('equipements.show', $equipement))
            ->assertOk()
            ->assertSee('Historique des entretiens')
            ->assertSee('Nettoyage filtre')
            ->assertSee('Entretiens réalisés');

        $this->actingAs($owner)->patch(route('equipements.entretiens.update', [$equipement, $entretien]), [
            'titre' => 'Filtre contrôlé',
            'type' => 'Contrôle',
            'date_prevue' => now()->toDateString(),
        ])->assertRedirect(route('equipements.show', $equipement));
        $this->assertDatabaseHas('entretiens_equipements', ['id' => $entretien->id, 'titre' => 'Filtre contrôlé']);

        $this->actingAs($owner)->delete(route('equipements.entretiens.destroy', [$equipement, $entretien]))
            ->assertRedirect(route('equipements.show', $equipement));
        $this->assertDatabaseMissing('entretiens_equipements', ['id' => $entretien->id]);
    }

    public function test_completed_maintenance_has_no_active_reminder(): void
    {
        $entretien = $this->entretienFor($this->equipementFor(User::factory()->create()), [
            'date_prevue' => today()->subDay(),
            'date_realisee' => today(),
        ]);

        $this->assertFalse($entretien->shouldShowReminder());
        $this->assertNull($entretien->reminderStatus());
    }

    public function test_future_maintenance_outside_its_reminder_period_has_no_reminder(): void
    {
        $entretien = $this->entretienFor($this->equipementFor(User::factory()->create()), [
            'date_prevue' => today()->addDays(10),
            'rappel_avant_jours' => 3,
        ]);

        $this->assertFalse($entretien->shouldShowReminder());
        $this->assertNull($entretien->reminderStatus());
    }

    public function test_future_maintenance_in_its_reminder_period_is_visible_on_the_equipment_page(): void
    {
        $owner = User::factory()->create();
        $equipement = $this->equipementFor($owner, 'Climatiseur du salon');
        $this->entretienFor($equipement, [
            'titre' => 'Contrôle avant saison chaude',
            'date_prevue' => today()->addDays(5),
            'rappel_avant_jours' => 5,
        ]);

        $this->actingAs($owner)->get(route('equipements.show', $equipement))
            ->assertOk()
            ->assertSee('Rappel d’entretien')
            ->assertSee('Contrôle avant saison chaude')
            ->assertSee('Prévu dans 5 jours.');
    }

    public function test_tomorrows_maintenance_has_a_tomorrow_reminder(): void
    {
        $entretien = $this->entretienFor($this->equipementFor(User::factory()->create()), [
            'date_prevue' => today()->addDay(),
            'rappel_avant_jours' => 3,
        ]);

        $this->assertSame('tomorrow', $entretien->reminderStatus());
        $this->assertSame('Entretien demain', $entretien->reminderTitle());
        $this->assertSame('Est prévu demain.', $entretien->reminderDescription());
    }

    public function test_todays_maintenance_has_a_today_reminder(): void
    {
        $entretien = $this->entretienFor($this->equipementFor(User::factory()->create()), [
            'date_prevue' => today(),
            'rappel_avant_jours' => 3,
        ]);

        $this->assertSame('today', $entretien->reminderStatus());
        $this->assertSame('Entretien prévu aujourd’hui', $entretien->reminderTitle());
        $this->assertSame('Est prévu aujourd’hui.', $entretien->reminderDescription());
    }

    public function test_uncompleted_past_maintenance_has_an_overdue_reminder(): void
    {
        $entretien = $this->entretienFor($this->equipementFor(User::factory()->create()), [
            'titre' => 'Nettoyage ventilation',
            'date_prevue' => today()->subDays(3),
        ]);

        $this->assertSame('overdue', $entretien->reminderStatus());
        $this->assertSame('Entretien en retard', $entretien->reminderTitle());
        $this->assertSame('En retard depuis 3 jours.', $entretien->reminderDescription());
    }

    public function test_user_dashboard_never_lists_reminders_for_another_users_equipment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $ownEquipement = $this->equipementFor($owner, 'Climatiseur privé');
        $otherEquipement = $this->equipementFor($other, 'PC étranger');
        $this->entretienFor($ownEquipement, ['titre' => 'Entretien propriétaire', 'date_prevue' => today()]);
        $this->entretienFor($otherEquipement, ['titre' => 'Entretien étranger', 'date_prevue' => today()->subDay()]);

        $this->actingAs($owner)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rappels d’entretien')
            ->assertSee('Climatiseur privé')
            ->assertSee('Entretien propriétaire')
            ->assertDontSee('PC étranger')
            ->assertDontSee('Entretien étranger');
    }

    public function test_other_user_cannot_manage_another_users_maintenance_or_analysis(): void
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $equipement = $this->equipementFor($owner);
        $entretien = $equipement->entretiens()->create([
            'titre' => 'Contrôle', 'type' => 'Contrôle', 'date_prevue' => now()->addWeek(),
        ]);

        $this->actingAs($visitor)->get(route('equipements.entretiens.edit', [$equipement, $entretien]))->assertNotFound();
        $this->actingAs($visitor)->patch(route('equipements.entretiens.realiser', [$equipement, $entretien]))->assertNotFound();
        $this->actingAs($visitor)->post(route('equipements.ai-analysis', $equipement))->assertNotFound();
    }

    public function test_equipment_analysis_uses_only_the_owners_context_and_handles_the_api_response(): void
    {
        config(['services.gemini.key' => 'test-key', 'services.gemini.model' => 'test-model']);
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $equipement = $this->equipementFor($owner, 'Climatiseur privé');
        $otherEquipement = $this->equipementFor($other, 'Équipement étranger');
        $equipement->conseils()->create([
            'titre' => 'Nettoyer les filtres', 'description' => 'Conseil privé.', 'categorie' => 'Chaleur', 'niveau' => 'Important', 'actif' => true,
        ]);
        $capturedBody = '';
        Http::fake(function (ClientRequest $request) use (&$capturedBody) {
            $capturedBody = $request->body();

            return Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => 'Niveau d’attention : élevé',
                        ]],
                    ],
                ]],
            ], 200);
        });

        $this->actingAs($owner)->post(route('equipements.ai-analysis', $equipement))
            ->assertRedirect(route('equipements.show', $equipement))
            ->assertSessionHas('ai_analysis', 'Niveau d’attention : élevé');

        $prompt = json_decode($capturedBody, true, 512, JSON_THROW_ON_ERROR)['contents'][0]['parts'][0]['text'];

        $this->assertStringContainsString('Climatiseur privé', $prompt);
        $this->assertStringNotContainsString($otherEquipement->nom, $prompt);
    }

    public function test_equipment_analysis_hides_an_api_failure_from_the_user(): void
    {
        config(['services.gemini.key' => 'test-key', 'services.gemini.model' => 'test-model']);
        $user = User::factory()->create();
        $equipement = $this->equipementFor($user);
        Http::fake(['*' => Http::response(['error' => 'unavailable'], 503)]);

        $this->actingAs($user)->post(route('equipements.ai-analysis', $equipement))
            ->assertRedirect(route('equipements.show', $equipement))
            ->assertSessionHas('ai_analysis', 'Assistant temporairement indisponible.');
    }

    private function equipementFor(User $user, string $name = 'Climatiseur salon'): Equipement
    {
        return $user->equipements()->create([
            'nom' => $name,
            'type' => 'Climatisation',
            'marque' => 'LG',
            'sensible_chaleur' => true,
            'sensible_coupure' => true,
        ]);
    }

    /** @param array<string, mixed> $attributes */
    private function entretienFor(Equipement $equipement, array $attributes = []): EntretienEquipement
    {
        return $equipement->entretiens()->create(array_merge([
            'titre' => 'Entretien de contrôle',
            'type' => 'Contrôle',
            'date_prevue' => today()->addWeek(),
            'rappel_avant_jours' => 3,
        ], $attributes));
    }
}
