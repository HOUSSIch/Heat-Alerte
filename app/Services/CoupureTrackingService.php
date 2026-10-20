<?php

namespace App\Services;

use App\Models\CoupureElectrique;
use Carbon\CarbonInterface;

class CoupureTrackingService
{
    /** @return array<string, int|string|null> */
    public function track(CoupureElectrique $coupure, ?CarbonInterface $now = null): array
    {
        $now ??= now();
        $start = $coupure->date_debut;
        $estimatedEnd = $coupure->date_fin_estimee;
        $actualEnd = $coupure->date_fin_reelle;

        if ($actualEnd) {
            return $this->result('Terminée', 100, $start?->diffForHumans($actualEnd, true), null, $start?->diffForHumans($actualEnd, true));
        }

        if (! $start || $now->lt($start)) {
            return $this->result('Programmée', 0, null, $start ? $now->diffForHumans($start, true) : null, null);
        }

        $elapsed = $start->diffForHumans($now, true);
        if (! $estimatedEnd) {
            return $this->result('En cours', 0, $elapsed, null, null);
        }

        if ($now->gt($estimatedEnd)) {
            return $this->result('En retard / prolongée', 100, $elapsed, $estimatedEnd->diffForHumans($now, true), null);
        }

        $total = max(1, $start->diffInSeconds($estimatedEnd));
        $progress = (int) min(100, max(0, round($start->diffInSeconds($now) / $total * 100)));

        return $this->result('En cours', $progress, $elapsed, $now->diffForHumans($estimatedEnd, true), null);
    }

    /** @return array<string, int|string|null> */
    private function result(string $status, int $progress, ?string $elapsed, ?string $remaining, ?string $actualDuration): array
    {
        return compact('status', 'progress', 'elapsed', 'remaining', 'actualDuration');
    }
}
