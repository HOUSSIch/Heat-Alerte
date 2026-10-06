<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointFraicheur;
use App\Models\Quartier;
use App\Services\GeoapifyService;
use Illuminate\Http\Request;
use RuntimeException;

class PointFraicheurController extends Controller
{
    public function index()
    {
        return view('admin.points-fraicheur.index', [
            'points' => PointFraicheur::with('quartier')->withCount('avis')->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.points-fraicheur.create', [
            'pointFraicheur' => new PointFraicheur(),
            'quartiers' => Quartier::where('actif', true)->orderBy('ville')->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request)
    {
        PointFraicheur::create($this->data($request) + ['source' => 'manuel']);

        return redirect()->route('admin.points-fraicheur.index')->with('success', 'Point de fraîcheur créé.');
    }

    public function show(PointFraicheur $pointFraicheur)
    {
        return view('admin.points-fraicheur.show', [
            'pointFraicheur' => $pointFraicheur->load('quartier')->loadCount('avis'),
        ]);
    }

    public function edit(PointFraicheur $pointFraicheur)
    {
        return view('admin.points-fraicheur.edit', [
            'pointFraicheur' => $pointFraicheur,
            'quartiers' => Quartier::where('actif', true)->orderBy('ville')->orderBy('nom')->get(),
        ]);
    }

    public function update(Request $request, PointFraicheur $pointFraicheur)
    {
        $pointFraicheur->update($this->data($request));

        return redirect()->route('admin.points-fraicheur.show', $pointFraicheur)->with('success', 'Point de fraîcheur modifié.');
    }

    public function destroy(PointFraicheur $pointFraicheur)
    {
        $pointFraicheur->delete();

        return redirect()->route('admin.points-fraicheur.index')->with('success', 'Point de fraîcheur supprimé.');
    }

    public function geoapify()
    {
        return view('admin.points-fraicheur.geoapify', [
            'quartiers' => Quartier::where('actif', true)->orderBy('ville')->orderBy('nom')->get(),
        ]);
    }

    public function searchGeoapify(Request $request, GeoapifyService $geoapify)
    {
        $data = $request->validate([
            'quartier_id' => ['required', 'exists:quartiers,id'],
            'radius' => ['required', 'integer', 'in:1000,3000,5000'],
        ]);
        $quartier = Quartier::findOrFail($data['quartier_id']);

        try {
            $suggestions = $geoapify->searchNearbyPlaces((float) $quartier->latitude, (float) $quartier->longitude, $data['radius']);
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['geoapify' => $exception->getMessage()]);
        }

        return view('admin.points-fraicheur.geoapify-results', compact('quartier', 'suggestions'));
    }

    public function importGeoapify(Request $request)
    {
        $data = $request->validate([
            'quartier_id' => ['required', 'exists:quartiers,id'],
            'place_id' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'categories' => ['nullable', 'string'],
        ]);

        if (PointFraicheur::where('geoapify_place_id', $data['place_id'])->exists()) {
            return back()->withErrors(['geoapify' => 'Ce lieu est déjà enregistré.']);
        }

        $categories = json_decode($data['categories'] ?? '[]', true) ?: [];
        $point = PointFraicheur::create([
            'quartier_id' => $data['quartier_id'],
            'nom' => $data['nom'],
            'type' => $this->typeFromCategories($categories),
            'adresse' => $data['adresse'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'actif' => true,
            'source' => 'geoapify',
            'geoapify_place_id' => $data['place_id'],
        ]);

        return redirect()->route('admin.points-fraicheur.show', $point)->with('success', 'Lieu Geoapify importé comme point de fraîcheur.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'quartier_id' => ['required', 'exists:quartiers,id'],
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'adresse' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string'],
            'horaires' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'climatise' => ['nullable', 'boolean'],
            'eau_disponible' => ['nullable', 'boolean'],
            'accessible_pmr' => ['nullable', 'boolean'],
            'actif' => ['nullable', 'boolean'],
        ]);

        foreach (['climatise', 'eau_disponible', 'accessible_pmr', 'actif'] as $field) {
            $data[$field] = $request->boolean($field);
        }

        return $data;
    }

    private function typeFromCategories(array $categories): string
    {
        return match (true) {
            in_array('leisure.park', $categories, true) => 'Parc',
            in_array('commercial.shopping_mall', $categories, true) => 'Centre commercial',
            in_array('activity.community_center', $categories, true) => 'Centre communautaire',
            default => 'Lieu de fraîcheur',
        };
    }
}
