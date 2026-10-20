<?php

namespace App\Services;

use App\Models\PointFraicheur;
use App\Models\Quartier;

class PointCoverageService
{
    public function analyse(Quartier $quartier): array
    {
        $points = PointFraicheur::where('quartier_id', $quartier->id)->where('actif', true)->get();
        $count = $points->count();
        $nearest = $points->map(fn ($p) => $this->distance((float) $quartier->latitude, (float) $quartier->longitude, (float) $p->latitude, (float) $p->longitude))->min();
        $level = $count >= 3 ? 'Bonne' : ($count >= 1 ? 'Moyenne' : 'Faible');

        return compact('points', 'count', 'nearest', 'level');
    }

    public function distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
