<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntretienRequest;
use App\Models\EntretienEquipement;
use App\Models\Equipement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntretienEquipementController extends Controller
{
    public function create(Request $request, Equipement $equipement): View
    {
        return view('entretiens.create', ['equipement' => $this->ownedEquipement($request, $equipement)]);
    }

    public function store(StoreEntretienRequest $request, Equipement $equipement): RedirectResponse
    {
        $equipement = $this->ownedEquipement($request, $equipement);
        $equipement->entretiens()->create($this->validatedData($request));

        return redirect()->route('equipements.show', $equipement)->with('success', 'Entretien ajouté avec succès.');
    }

    public function edit(Request $request, Equipement $equipement, EntretienEquipement $entretien): View
    {
        return view('entretiens.edit', [
            'equipement' => $this->ownedEquipement($request, $equipement),
            'entretien' => $this->ownedEntretien($equipement, $entretien),
        ]);
    }

    public function update(StoreEntretienRequest $request, Equipement $equipement, EntretienEquipement $entretien): RedirectResponse
    {
        $equipement = $this->ownedEquipement($request, $equipement);
        $entretien = $this->ownedEntretien($equipement, $entretien);
        $entretien->update($this->validatedData($request));

        return redirect()->route('equipements.show', $equipement)->with('success', 'Entretien modifié avec succès.');
    }

    public function destroy(Request $request, Equipement $equipement, EntretienEquipement $entretien): RedirectResponse
    {
        $equipement = $this->ownedEquipement($request, $equipement);
        $this->ownedEntretien($equipement, $entretien)->delete();

        return redirect()->route('equipements.show', $equipement)->with('success', 'Entretien supprimé.');
    }

    public function realiser(Request $request, Equipement $equipement, EntretienEquipement $entretien): RedirectResponse
    {
        $equipement = $this->ownedEquipement($request, $equipement);
        $this->ownedEntretien($equipement, $entretien)->update(['date_realisee' => now()->toDateString()]);

        return redirect()->route('equipements.show', $equipement)->with('success', 'Entretien marqué comme réalisé.');
    }

    private function ownedEquipement(Request $request, Equipement $equipement): Equipement
    {
        return $request->user()->equipements()->findOrFail($equipement->id);
    }

    private function ownedEntretien(Equipement $equipement, EntretienEquipement $entretien): EntretienEquipement
    {
        return $equipement->entretiens()->findOrFail($entretien->id);
    }

    /** @return array<string, mixed> */
    private function validatedData(StoreEntretienRequest $request): array
    {
        return $request->validated();
    }
}
