<?php

namespace Database\Factories;

use App\Models\Equipement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipement>
 */
class EquipementFactory extends Factory
{
    protected $model = Equipement::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nom' => fake()->unique()->words(3, true),
            'type' => fake()->randomElement(['Climatisation', 'Électroménager', 'Informatique']),
            'marque' => fake()->company(),
            'sensible_chaleur' => fake()->boolean(),
            'sensible_coupure' => fake()->boolean(),
            'description' => fake()->sentence(),
        ];
    }
}
