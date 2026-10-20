<?php

namespace App\Services;

use App\Models\EntretienEquipement;
use App\Models\Equipement;
use Carbon\CarbonInterface;

class EquipementQrService
{
    public function buildPayload(Equipement $equipement): string
    {
        $entretiens = $equipement->entretiens;
        $prochain = $entretiens
            ->filter(fn (EntretienEquipement $entretien) => ! $entretien->date_realisee && $entretien->date_prevue->gte(today()))
            ->sortBy('date_prevue')
            ->first();
        $enRetard = $entretiens->contains(fn (EntretienEquipement $entretien) => $entretien->statut() === 'en_retard');
        $conseil = $equipement->conseils->first();

        return implode("\n", [
            'HEATALERT - PASSEPORT EQUIPEMENT',
            'ID : '.$this->publicReference($equipement),
            'Nom : '.$equipement->nom,
            'Type : '.$equipement->type,
            'Marque : '.($equipement->marque ?: 'Non renseignée'),
            'Sensible à la chaleur : '.($equipement->sensible_chaleur ? 'Oui' : 'Non'),
            'Sensible aux coupures : '.($equipement->sensible_coupure ? 'Oui' : 'Non'),
            'État entretien : '.($enRetard ? 'En retard' : ($prochain ? 'Prévu' : 'À jour')),
            'Prochain entretien : '.($prochain ? $this->formatDate($prochain->date_prevue) : 'Aucun planifié'),
            'Conseil principal : '.($conseil?->titre ?: 'Aucun conseil associé.'),
            'HeatAlert - Équipement appartenant à un compte utilisateur privé.',
        ]);
    }

    public function publicReference(Equipement $equipement): string
    {
        return sprintf('EQ-%04d', $equipement->id);
    }

    private function formatDate(CarbonInterface $date): string
    {
        return $date->format('d/m/Y');
    }
}
