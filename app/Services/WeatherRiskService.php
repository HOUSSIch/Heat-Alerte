<?php

namespace App\Services;

class WeatherRiskService
{
    public function level(?float $temperature): string
    {
        if ($temperature === null || $temperature < config('heatalert.weather_risk.vigilance')) {
            return 'Normal';
        }

        if ($temperature < config('heatalert.weather_risk.alerte')) {
            return 'Vigilance';
        }

        if ($temperature < config('heatalert.weather_risk.critique')) {
            return 'Alerte';
        }

        return 'Critique';
    }
}
