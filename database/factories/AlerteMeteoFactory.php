<?php

namespace Database\Factories;

use App\Models\AlerteMeteo;
use App\Models\Quartier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlerteMeteo>
 */
class AlerteMeteoFactory extends Factory
{
    protected $model = AlerteMeteo::class;

    public function definition(): array
    {
        return [
            'quartier_id' => Quartier::factory(),
            'titre' => 'Vigilance météo : '.fake()->words(3, true),
            'description' => fake()->paragraph(),
            'temperature' => fake()->randomFloat(1, 30, 42),
            'temperature_min' => fake()->randomFloat(1, 22, 29),
            'temperature_max' => fake()->randomFloat(1, 34, 45),
            'niveau' => fake()->randomElement(['Normal', 'Vigilance', 'Alerte']),
            'source' => 'manuel',
            'date_debut' => now(),
            'date_fin' => now()->addDay(),
            'actif' => true,
        ];
    }
}
