<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EtablissementSensible;
use App\Models\Quartier;
use Illuminate\Http\Request;

class EtablissementSensibleController extends Controller
{
    public function index()
    {
        return view('admin.etablissements-sensibles.index', [
            'etablissements' => EtablissementSensible::with('quartier')->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.etablissements-sensibles.create', [
            'etablissementSensible' => new EtablissementSensible,
            'quartiers' => $this->quartiersActifs(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quartier_id' => ['required', 'exists:quartiers,id'],
            'nom' => ['bail', 'required', 'string', 'min:2', 'max:150'],
            'type' => ['bail', 'required', 'string', 'min:2', 'max:100'],
            'adresse' => ['bail', 'required', 'string', 'min:3', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:5000'],
            'horaires' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'string', 'max:5000'],
            'accueil_public' => ['nullable', 'boolean'],
            'actif' => ['nullable', 'boolean'],
        ]);
        $validated['actif'] = $request->boolean('actif');
        $validated['accueil_public'] = $request->boolean('accueil_public');

        EtablissementSensible::create($validated);

        return redirect()
            ->route('admin.etablissements-sensibles.index')
            ->with('success', 'Établissement sensible créé.');
    }

    public function show(EtablissementSensible $etablissementSensible)
    {
        return view('admin.etablissements-sensibles.show', [
            'etablissementSensible' => $etablissementSensible->load('quartier'),
        ]);
    }

    public function edit(EtablissementSensible $etablissementSensible)
    {
        return view('admin.etablissements-sensibles.edit', [
            'etablissementSensible' => $etablissementSensible,
            'quartiers' => $this->quartiersActifs(),
        ]);
    }

    public function update(Request $request, EtablissementSensible $etablissementSensible)
    {
        $validated = $request->validate([
            'quartier_id' => ['required', 'exists:quartiers,id'],
            'nom' => ['bail', 'required', 'string', 'min:2', 'max:150'],
            'type' => ['bail', 'required', 'string', 'min:2', 'max:100'],
            'adresse' => ['bail', 'required', 'string', 'min:3', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:5000'],
            'horaires' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'string', 'max:5000'],
            'accueil_public' => ['nullable', 'boolean'],
            'actif' => ['nullable', 'boolean'],
        ]);
        $validated['actif'] = $request->boolean('actif');
        $validated['accueil_public'] = $request->boolean('accueil_public');

        $etablissementSensible->update($validated);

        return redirect()
            ->route('admin.etablissements-sensibles.show', $etablissementSensible)
            ->with('success', 'Établissement sensible modifié.');
    }

    public function destroy(EtablissementSensible $etablissementSensible)
    {
        $etablissementSensible->delete();

        return redirect()
            ->route('admin.etablissements-sensibles.index')
            ->with('success', 'Établissement sensible supprimé.');
    }

    private function quartiersActifs()
    {
        return Quartier::where('actif', true)->orderBy('ville')->orderBy('nom')->get();
    }
}
