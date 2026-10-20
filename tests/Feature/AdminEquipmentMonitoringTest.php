<?php

namespace Tests\Feature;

use App\Models\EntretienEquipement;
use App\Models\Equipement;
use App\Models\User;
use App\Services\AdminAssistantContextService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminEquipmentMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_consult_equipment_list_filter_it_and_open_read_only_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['name' => 'Meriem', 'email' => 'meriem@example.test']);
        $clim = $this->equipmentFor($owner, ['nom' => 'Climatiseur salon', 'type' => 'Climatisation', 'sensible_coupure' => true]);
        $fridge = $this->equipmentFor($owner, ['nom' => 'Réfrigérateur cuisine', 'type' => 'Électroménager']);
        $this->maintenanceFor($clim, ['date_prevue' => today()->subDays(2)]);

        $this->actingAs($admin)->get(route('admin.equipements.index', ['type' => 'Climatisation', 'sensible_coupure' => 1, 'statut_entretien' => 'en_retard']))
            ->assertOk()
            ->assertSee('Climatiseur salon')
            ->assertDontSee('Réfrigérateur cuisine')
            ->assertSee('EQ-'.str_pad((string) $clim->id, 4, '0', STR_PAD_LEFT));

        $this->actingAs($admin)->get(route('admin.equipements.show', $clim))
            ->assertOk()
            ->assertSee('Meriem')
            ->assertSee('Historique des entretiens')
            ->assertSee('Consultation administrative en lecture seule.')
            ->assertDontSee('Modifier')
            ->assertDontSee('Supprimer');
    }

    public function test_habitant_cannot_access_admin_equipment_entretien_or_assistant_pages(): void
    {
        $habitant = User::factory()->create(['role' => 'habitant']);

        $this->actingAs($habitant)->get(route('admin.equipements.index'))->assertForbidden();
        $this->actingAs($habitant)->get(route('admin.entretiens.index'))->assertForbidden();
        $this->actingAs($habitant)->get(route('admin.assistant.index'))->assertForbidden();
    }

    public function test_admin_equipment_list_is_paginated(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();

        foreach (range(1, 16) as $number) {
            $this->equipmentFor($owner, ['nom' => 'Équipement '.$number]);
        }

        $response = $this->actingAs($admin)->get(route('admin.equipements.index'));

        $response->assertOk();
        $this->assertSame(15, $response->viewData('equipements')->perPage());
        $this->assertSame(16, $response->viewData('equipements')->total());
    }

    public function test_admin_can_filter_maintenance_list_and_overdue_status_is_visible(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $overdueEquipment = $this->equipmentFor($owner, ['nom' => 'PC portable']);
        $plannedEquipment = $this->equipmentFor($owner, ['nom' => 'Téléviseur']);
        $this->maintenanceFor($overdueEquipment, ['titre' => 'Nettoyage ventilation', 'date_prevue' => today()->subDays(3)]);
        $this->maintenanceFor($plannedEquipment, ['titre' => 'Contrôle écran', 'date_prevue' => today()->addDays(3)]);

        $this->actingAs($admin)->get(route('admin.entretiens.index', ['statut' => 'en_retard', 'user_id' => $owner->id]))
            ->assertOk()
            ->assertSee('Nettoyage ventilation')
            ->assertSee('En retard')
            ->assertDontSee('Contrôle écran');
    }

    public function test_admin_dashboard_exposes_equipment_and_maintenance_statistics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $equipment = $this->equipmentFor($owner, ['sensible_chaleur' => true, 'sensible_coupure' => true]);
        $this->maintenanceFor($equipment, ['date_prevue' => today()->subDay()]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Sensibles chaleur')
            ->assertSee('Sensibles coupures')
            ->assertSee('Entretiens en retard')
            ->assertSee('Prévus cette semaine');
    }

    public function test_admin_assistant_uses_a_sanitized_global_context_and_handles_api_errors(): void
    {
        config(['services.gemini.key' => 'secret-key-that-must-not-leak', 'services.gemini.model' => 'test-model']);
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['name' => 'Dossier utile']);
        $equipment = $this->equipmentFor($owner, ['nom' => 'Climatiseur prioritaire']);
        $this->maintenanceFor($equipment, ['titre' => 'Contrôle urgent', 'date_prevue' => today()->subDays(4)]);
        $capturedPrompt = '';
        Http::fake(function (ClientRequest $request) use (&$capturedPrompt) {
            $capturedPrompt = $request->body();

            return Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => '1 entretien prioritaire identifié.',
                        ]],
                    ],
                ]],
            ]);
        });

        $this->actingAs($admin)->post(route('admin.assistant.send'), ['question' => 'Quels équipements sont urgents ?'])
            ->assertRedirect(route('admin.assistant.index'))
            ->assertSessionHas('admin_chat_history.1.content', '1 entretien prioritaire identifié.');

        $this->assertStringContainsString('total_equipements', $capturedPrompt);
        $this->assertStringContainsString('entretiens_en_retard', $capturedPrompt);
        $this->assertStringContainsString('Climatiseur prioritaire', $capturedPrompt);
        $this->assertStringNotContainsString('password', strtolower($capturedPrompt));
        $this->assertStringNotContainsString('secret-key-that-must-not-leak', $capturedPrompt);

    }

    public function test_admin_assistant_handles_an_api_error_cleanly(): void
    {
        config(['services.gemini.key' => 'test-key', 'services.gemini.model' => 'test-model']);
        $admin = User::factory()->create(['role' => 'admin']);
        Http::fake(['*' => Http::response(['error' => 'indisponible'], 503)]);

        $this->actingAs($admin)->post(route('admin.assistant.send'), ['question' => 'Nouvelle question'])
            ->assertRedirect(route('admin.assistant.index'))
            ->assertSessionHas('admin_chat_history.1.content', 'Le service Assistant HeatAlert est momentanément indisponible.');
    }

    public function test_admin_context_contains_only_synthetic_operational_data(): void
    {
        $owner = User::factory()->create();
        $equipment = $this->equipmentFor($owner, ['nom' => 'Équipement suivi']);
        $this->maintenanceFor($equipment, ['date_prevue' => today()->subDay()]);

        $context = app(AdminAssistantContextService::class)->buildContext();
        $serialized = json_encode($context, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('statistiques', $context);
        $this->assertArrayHasKey('entretiens_prioritaires', $context);
        $this->assertStringContainsString('total_equipements', $serialized);
        $this->assertStringNotContainsString('password', strtolower($serialized));
        $this->assertStringNotContainsString('remember_token', strtolower($serialized));
    }

    /** @param array<string, mixed> $attributes */
    private function equipmentFor(User $owner, array $attributes = []): Equipement
    {
        return $owner->equipements()->create(array_merge([
            'nom' => 'Équipement',
            'type' => 'Climatisation',
            'marque' => 'LG',
            'sensible_chaleur' => false,
            'sensible_coupure' => false,
        ], $attributes));
    }

    /** @param array<string, mixed> $attributes */
    private function maintenanceFor(Equipement $equipment, array $attributes = []): EntretienEquipement
    {
        return $equipment->entretiens()->create(array_merge([
            'titre' => 'Entretien',
            'type' => 'Contrôle',
            'date_prevue' => today()->addDay(),
            'rappel_avant_jours' => 3,
        ], $attributes));
    }
}
