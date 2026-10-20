<?php

namespace App\Services;

use App\Models\AlerteMeteo;
use App\Models\Quartier;
use App\Notifications\WeatherVigilanceNotification;

class WeatherVigilanceService
{
    public function __construct(
        private readonly OpenMeteoService $meteo,
        private readonly WeatherRiskService $risk,
    ) {}

    /** @return array<string, mixed> */
    public function forQuartier(Quartier $quartier, bool $refresh = false): array
    {
        $forecast = $this->meteo->forecast($quartier, $refresh);
        $days = collect($forecast['days'])->map(fn (array $day) => $day + [
            'niveau' => $this->risk->level($day['temperature_max']),
        ])->all();

        return $forecast + [
            'days' => $days,
            'niveau' => $this->risk->level($forecast['temperature']),
        ];
    }

    public function updateAlert(Quartier $quartier, array $vigilance): ?AlerteMeteo
    {
        $tomorrow = $vigilance['days'][1] ?? $vigilance['days'][0] ?? null;
        if (! $tomorrow || ! in_array($tomorrow['niveau'], ['Alerte', 'Critique'], true)) {
            return null;
        }

        $alerte = AlerteMeteo::updateOrCreate(
            ['quartier_id' => $quartier->id, 'source' => 'open-meteo', 'actif' => true],
            [
                'titre' => 'Forte chaleur à '.$quartier->nom,
                'description' => 'Prévision HeatAlert issue d’Open-Meteo pour le '.$tomorrow['date'].'.',
                'temperature' => $vigilance['temperature'],
                'temperature_max' => $tomorrow['temperature_max'],
                'temperature_min' => $tomorrow['temperature_min'],
                'niveau' => $tomorrow['niveau'],
                'date_debut' => now(),
                'date_fin' => now()->endOfDay(),
            ],
        );

        if ($alerte->wasRecentlyCreated || $alerte->wasChanged('niveau')) {
            $quartier->users()->each(fn ($user) => $user->notify(new WeatherVigilanceNotification($alerte)));
        }

        return $alerte;
    }
}
