<?php

namespace App\Services;

use App\Models\Conseil;
use App\Models\EntretienEquipement;
use App\Models\Equipement;

class AdminAssistantContextService
{
    /** @return array<string, mixed> */
    public function buildContext(): array
    {
        $overdue = EntretienEquipement::query()
            ->with('equipement:id,nom,type,user_id')
            ->whereNull('date_realisee')
            ->whereDate('date_prevue', '<', today())
            ->orderBy('date_prevue')
            ->take(8)
            ->get();

        return [
            'statistiques' => [
                'total_equipements' => Equipement::count(),
                'sensibles_coupures' => Equipement::where('sensible_coupure', true)->count(),
                'sensibles_chaleur' => Equipement::where('sensible_chaleur', true)->count(),
                'entretiens_en_retard' => EntretienEquipement::whereNull('date_realisee')->whereDate('date_prevue', '<', today())->count(),
                'conseils_actifs' => Conseil::where('actif', true)->count(),
                'equipements_par_type' => Equipement::query()->selectRaw('type, count(*) as total')->groupBy('type')->orderByDesc('total')->get()->map(fn ($row) => ['type' => $row->type, 'total' => $row->total])->all(),
            ],
            'entretiens_prioritaires' => $overdue->map(fn (EntretienEquipement $entretien) => [
                'reference' => sprintf('EQ-%04d', $entretien->equipement_id),
                'equipement' => $entretien->equipement?->nom,
                'type' => $entretien->equipement?->type,
                'entretien' => $entretien->titre,
                'retard_jours' => $entretien->date_prevue->diffInDays(today(), true),
            ])->all(),
            'conseils_actifs' => Conseil::where('actif', true)->orderBy('titre')->take(10)->get(['titre', 'categorie', 'niveau'])->map->only(['titre', 'categorie', 'niveau'])->all(),
        ];
    }
}
