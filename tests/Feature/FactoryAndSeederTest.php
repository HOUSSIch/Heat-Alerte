<?php

namespace Tests\Feature;

use App\Models\AlerteMeteo;
use App\Models\Avis;
use App\Models\Conseil;
use App\Models\CoupureElectrique;
use App\Models\Equipement;
use App\Models\PointFraicheur;
use App\Models\Quartier;
use App\Models\Signalement;
use App\Models\User;
use Database\Seeders\HeatAlertDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoryAndSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_relational_factories_create_a_coherent_graph(): void
    {
        $quartier = Quartier::factory()->create();
        $user = User::factory()->for($quartier)->create();
        $equipement = Equipement::factory()->for($user)->create();
        $conseil = Conseil::factory()->create();
        $conseil->equipements()->sync([$equipement->id]);
        $alerte = AlerteMeteo::factory()->for($quartier)->create();
        $coupure = CoupureElectrique::factory()->for($quartier)->create();
        $signalement = Signalement::factory()
            ->for($user)
            ->for($quartier)
            ->for($coupure, 'coupureElectrique')
            ->create();
        $point = PointFraicheur::factory()->for($quartier)->create();
        $avis = Avis::factory()->for($user)->for($point, 'pointFraicheur')->create();

        $this->assertTrue($user->quartier->is($quartier));
        $this->assertTrue($equipement->user->is($user));
        $this->assertTrue($conseil->equipements->first()->is($equipement));
        $this->assertTrue($alerte->quartier->is($quartier));
        $this->assertTrue($coupure->quartier->is($quartier));
        $this->assertTrue($signalement->coupureElectrique->is($coupure));
        $this->assertTrue($avis->pointFraicheur->is($point));
    }

    public function test_demo_seeder_is_rerunnable_and_creates_the_documented_scenario(): void
    {
        $this->seed(HeatAlertDemoSeeder::class);
        $this->seed(HeatAlertDemoSeeder::class);

        $bardo = Quartier::where('nom', 'Le Bardo')->where('ville', 'Tunis')->firstOrFail();
        $habitant = User::where('email', 'habitant@heatalert.test')->firstOrFail();
        $equipement = Equipement::where('user_id', $habitant->id)
            ->where('nom', 'Réfrigérateur principal')
            ->firstOrFail();

        $this->assertDatabaseHas('users', ['email' => 'admin@heatalert.test', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'agent@heatalert.test', 'role' => 'agent']);
        $this->assertSame(1, PointFraicheur::where('quartier_id', $bardo->id)->where('nom', 'Parc du Bardo')->count());
        $this->assertDatabaseHas('conseil_equipement', ['equipement_id' => $equipement->id]);
        $this->assertDatabaseHas('signalements', ['user_id' => $habitant->id, 'quartier_id' => $bardo->id]);
        $this->assertDatabaseHas('avis', ['user_id' => $habitant->id, 'statut' => 'publie']);
    }
}
