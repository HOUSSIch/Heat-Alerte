<?php

namespace Database\Factories;

use App\Models\Conseil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conseil>
 */
class ConseilFactory extends Factory
{
    protected $model = Conseil::class;

    public function definition(): array
    {
        return [
            'titre' => fake()->unique()->sentence(4),
            'description' => fake()->paragraph(),
            'categorie' => fake()->randomElement(['Chaleur', 'Coupure électrique', 'Prévention']),
            'niveau' => fake()->randomElement(['Information', 'Important', 'Urgent']),
            'actif' => true,
        ];
    }
}
