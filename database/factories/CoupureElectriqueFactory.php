<?php

namespace Database\Factories;

use App\Models\CoupureElectrique;
use App\Models\Quartier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CoupureElectrique>
 */
class CoupureElectriqueFactory extends Factory
{
    protected $model = CoupureElectrique::class;

    public function definition(): array
    {
        return [
            'quartier_id' => Quartier::factory(),
            'titre' => 'Intervention réseau '.fake()->words(2, true),
            'description' => fake()->paragraph(),
            'type' => 'maintenance',
            'cause' => 'Maintenance préventive',
            'statut' => 'prévue',
            'date_debut' => now()->addDay(),
            'date_fin_estimee' => now()->addDay()->addHours(3),
            'adresse' => fake()->address(),
            'latitude' => fake()->latitude(36.7, 36.9),
            'longitude' => fake()->longitude(10.1, 10.3),
            'source' => 'admin',
            'actif' => true,
        ];
    }
}
