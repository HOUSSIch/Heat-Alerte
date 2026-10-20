<?php

namespace Database\Factories;

use App\Models\CoupureElectrique;
use App\Models\Quartier;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Signalement>
 */
class SignalementFactory extends Factory
{
    protected $model = Signalement::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'coupure_electrique_id' => CoupureElectrique::factory(),
            'quartier_id' => Quartier::factory(),
            'titre' => 'Signalement : '.fake()->words(3, true),
            'description' => fake()->paragraph(),
            'adresse' => fake()->address(),
            'latitude' => fake()->latitude(36.7, 36.9),
            'longitude' => fake()->longitude(10.1, 10.3),
            'statut' => 'en_attente',
            'date_signalement' => now(),
        ];
    }
}
