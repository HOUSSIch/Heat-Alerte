<?php

namespace Database\Factories;

use App\Models\PointFraicheur;
use App\Models\Quartier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PointFraicheur>
 */
class PointFraicheurFactory extends Factory
{
    protected $model = PointFraicheur::class;

    public function definition(): array
    {
        return [
            'quartier_id' => Quartier::factory(),
            'nom' => 'Espace frais '.fake()->unique()->word(),
            'type' => 'Centre / espace climatisé',
            'adresse' => fake()->address(),
            'latitude' => fake()->latitude(36.7, 36.9),
            'longitude' => fake()->longitude(10.1, 10.3),
            'description' => fake()->sentence(),
            'horaires' => '08:00 - 18:00',
            'climatise' => true,
            'eau_disponible' => true,
            'accessible_pmr' => true,
            'actif' => true,
            'source' => 'manuel',
        ];
    }
}
