<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipementController extends Controller
{
    public function index(Request $request): View
    {
        $equipements = $request->user()->equipements()->latest()->get();

        return view('equipements.index', compact('equipements'));
    }

    public function create(): View
    {
        return view('equipements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->equipements()->create($this->validatedData($request));

        return redirect()->route('equipements.index')->with('success', 'Équipement ajouté avec succès.');
    }

    public function show(Request $request, Equipement $equipement): View
    {
        return view('equipements.show', ['equipement' => $this->ownedEquipement($request, $equipement)]);
    }

    public function edit(Request $request, Equipement $equipement): View
    {
        return view('equipements.edit', ['equipement' => $this->ownedEquipement($request, $equipement)]);
    }

    public function update(Request $request, Equipement $equipement): RedirectResponse
    {
        $equipement = $this->ownedEquipement($request, $equipement);
        $equipement->update($this->validatedData($request));

        return redirect()->route('equipements.show', $equipement)->with('success', 'Équipement modifié avec succès.');
    }

    public function destroy(Request $request, Equipement $equipement): RedirectResponse
    {
        $this->ownedEquipement($request, $equipement)->delete();

        return redirect()->route('equipements.index')->with('success', 'Équipement supprimé avec succès.');
    }

    private function ownedEquipement(Request $request, Equipement $equipement): Equipement
    {
        return $request->user()->equipements()->findOrFail($equipement->id);
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'marque' => ['nullable', 'string', 'max:255'],
            'sensible_chaleur' => ['nullable', 'boolean'],
            'sensible_coupure' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['sensible_chaleur'] = $request->boolean('sensible_chaleur');
        $validated['sensible_coupure'] = $request->boolean('sensible_coupure');

        return $validated;
    }
}
