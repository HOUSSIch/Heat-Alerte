<?php

namespace Database\Factories;

use App\Models\Avis;
use App\Models\PointFraicheur;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Avis>
 */
class AvisFactory extends Factory
{
    protected $model = Avis::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'point_fraicheur_id' => PointFraicheur::factory(),
            'note' => fake()->numberBetween(1, 5),
            'commentaire' => fake()->sentence(),
            'statut' => 'publie',
        ];
    }
}
