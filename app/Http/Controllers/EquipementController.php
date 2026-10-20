<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipementRequest;
use App\Models\Equipement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function store(StoreEquipementRequest $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('equipements', 'public');
        }

        $request->user()->equipements()->create($data);

        return redirect()->route('equipements.index')->with('success', 'Équipement ajouté avec succès.');
    }

    public function show(Request $request, Equipement $equipement): View
    {
        $equipement = $this->ownedEquipement($request, $equipement);
        $equipement->load([
            'conseils' => fn ($query) => $query->where('actif', true),
            'entretiens' => fn ($query) => $query->orderByDesc('date_realisee')->orderBy('date_prevue'),
        ]);
        $rappelsEntretien = $equipement->entretiens
            ->filter(fn ($entretien) => $entretien->shouldShowReminder())
            ->sortBy(fn ($entretien) => sprintf('%02d-%010d', $entretien->reminderPriority(), $entretien->date_prevue->timestamp))
            ->take(3)
            ->values();

        return view('equipements.show', compact('equipement', 'rappelsEntretien'));
    }

    public function edit(Request $request, Equipement $equipement): View
    {
        return view('equipements.edit', ['equipement' => $this->ownedEquipement($request, $equipement)]);
    }

    public function update(StoreEquipementRequest $request, Equipement $equipement): RedirectResponse
    {
        $equipement = $this->ownedEquipement($request, $equipement);
        $data = $this->validatedData($request);

        if ($request->hasFile('photo')) {
            if ($equipement->photo) {
                Storage::disk('public')->delete($equipement->photo);
            }

            $data['photo'] = $request->file('photo')->store('equipements', 'public');
        }

        $equipement->update($data);

        return redirect()->route('equipements.show', $equipement)->with('success', 'Équipement modifié avec succès.');
    }

    public function destroy(Request $request, Equipement $equipement): RedirectResponse
    {
        $equipement = $this->ownedEquipement($request, $equipement);

        if ($equipement->photo) {
            Storage::disk('public')->delete($equipement->photo);
        }

        $equipement->delete();

        return redirect()->route('equipements.index')->with('success', 'Équipement supprimé avec succès.');
    }

    private function ownedEquipement(Request $request, Equipement $equipement): Equipement
    {
        return $request->user()->equipements()->findOrFail($equipement->id);
    }

    /** @return array<string, mixed> */
    private function validatedData(StoreEquipementRequest $request): array
    {
        $validated = $request->validated();

        unset($validated['photo']);

        $validated['sensible_chaleur'] = $request->boolean('sensible_chaleur');
        $validated['sensible_coupure'] = $request->boolean('sensible_coupure');

        return $validated;
    }
}
