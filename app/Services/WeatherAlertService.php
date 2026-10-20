<?php

namespace App\Services;

class WeatherAlertService
{
    public function niveau(?float $temperature): string
    {
        return app(WeatherRiskService::class)->level($temperature);
    }
}
