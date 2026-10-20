<?php

namespace Database\Factories;

use App\Models\Quartier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quartier>
 */
class QuartierFactory extends Factory
{
    protected $model = Quartier::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->city(),
            'ville' => 'Tunis',
            'code_postal' => fake()->postcode(),
            'latitude' => fake()->latitude(36.7, 36.9),
            'longitude' => fake()->longitude(10.1, 10.3),
            'actif' => true,
        ];
    }
}
