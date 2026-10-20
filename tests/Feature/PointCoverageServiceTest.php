<?php

namespace Tests\Feature;

use App\Models\PointFraicheur;
use App\Models\Quartier;
use App\Services\PointCoverageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointCoverageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_coverage_levels_and_nearest_distance_are_calculated_from_active_points(): void
    {
        $quartier = Quartier::factory()->create(['latitude' => 36.8, 'longitude' => 10.1]);

        $empty = app(PointCoverageService::class)->analyse($quartier);
        $this->assertSame('Faible', $empty['level']);
        $this->assertSame(0, $empty['count']);
        $this->assertNull($empty['nearest']);

        PointFraicheur::factory()->create(['quartier_id' => $quartier->id, 'actif' => true, 'latitude' => 36.81, 'longitude' => 10.11]);
        PointFraicheur::factory()->create(['quartier_id' => $quartier->id, 'actif' => false, 'latitude' => 36.82, 'longitude' => 10.12]);

        $average = app(PointCoverageService::class)->analyse($quartier);
        $this->assertSame('Moyenne', $average['level']);
        $this->assertSame(1, $average['count']);
        $this->assertGreaterThan(0, $average['nearest']);

        PointFraicheur::factory()->count(2)->create(['quartier_id' => $quartier->id, 'actif' => true, 'latitude' => 36.8, 'longitude' => 10.1]);

        $good = app(PointCoverageService::class)->analyse($quartier);
        $this->assertSame('Bonne', $good['level']);
        $this->assertSame(3, $good['count']);
        $this->assertSame(0.0, $good['nearest']);
    }
}
