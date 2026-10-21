<?php

namespace Tests\Feature;

use App\Models\AlerteMeteo;
use App\Models\Avis;
use App\Models\CommentairePublication;
use App\Models\Conseil;
use App\Models\CoupureElectrique;
use App\Models\Equipement;
use App\Models\PointFraicheur;
use App\Models\PublicationQuartier;
use App\Models\Quartier;
use App\Models\Signalement;
use App\Models\User;
use Database\Seeders\ProductionDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductionDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_demo_seeder_is_rerunnable_and_creates_a_coherent_demo_graph(): void
    {
        $adminEmail = 'admin-'.Str::lower(Str::random(12)).'@example.test';
        $adminPassword = Str::random(32);
        $agentPassword = Str::random(32);
        $userPassword = Str::random(32);

        config()->set('demo.admin_email', $adminEmail);
        config()->set('demo.admin_password', $adminPassword);
        config()->set('demo.agent_password', $agentPassword);
        config()->set('demo.user_password', $userPassword);

        $this->seed(ProductionDemoSeeder::class);
        $this->seed(ProductionDemoSeeder::class);

        $admin = User::where('email', $adminEmail)->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check($adminPassword, $admin->password));
        $this->assertGreaterThanOrEqual(10, Quartier::count());
        $this->assertGreaterThanOrEqual(28, User::count());
        $this->assertGreaterThanOrEqual(15, AlerteMeteo::count());
        $this->assertGreaterThanOrEqual(15, CoupureElectrique::count());
        $this->assertGreaterThanOrEqual(30, Signalement::count());
        $this->assertGreaterThanOrEqual(18, PointFraicheur::count());
        $this->assertGreaterThanOrEqual(30, Avis::count());
        $this->assertGreaterThanOrEqual(20, Equipement::count());
        $this->assertSame(1, User::where('email', $adminEmail)->count());

        $primaryHabitant = User::where('email', 'habitant-demo-1@heatalert.demo')->firstOrFail();
        $primaryEquipementIds = Equipement::where('user_id', $primaryHabitant->id)->pluck('id');

        $this->assertGreaterThanOrEqual(6, $primaryEquipementIds->count());
        $this->assertGreaterThanOrEqual(10, Conseil::whereHas('equipements', fn ($query) => $query->whereIn('equipements.id', $primaryEquipementIds))->count());
        $this->assertGreaterThanOrEqual(7, Signalement::where('user_id', $primaryHabitant->id)->count());
        $this->assertGreaterThanOrEqual(6, Avis::where('user_id', $primaryHabitant->id)->count());
        $this->assertGreaterThanOrEqual(3, PublicationQuartier::where('user_id', $primaryHabitant->id)->count());
        $this->assertGreaterThanOrEqual(3, CommentairePublication::where('user_id', $primaryHabitant->id)->count());
        $this->assertGreaterThanOrEqual(3, $primaryHabitant->confirmationsPublication()->count());
    }
}
