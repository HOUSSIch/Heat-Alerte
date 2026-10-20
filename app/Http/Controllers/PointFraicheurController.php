<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvisRequest;
use App\Models\Avis;
use App\Models\PointFraicheur;
use App\Services\PointCoverageService;
use Illuminate\Http\Request;

class PointFraicheurController extends Controller
{
    public function index(Request $request)
    {
        $quartier = $request->user()->quartier;
        $points = collect();

        if ($quartier) {
            $points = $this->pointsQuery($quartier->id)->get();
        }

        return view('points-fraicheur.index', [
            'quartier' => $quartier,
            'points' => $points,
            'meilleureNote' => $points->max('note_moyenne'),
            'mapPoints' => $points->map(fn ($point) => [
                'nom' => $point->nom,
                'type' => $point->type,
                'adresse' => $point->adresse,
                'note' => $point->note_moyenne,
                'latitude' => (float) $point->latitude,
                'longitude' => (float) $point->longitude,
                'url' => route('points-fraicheur.show', $point),
            ])->values(),
        ]);
    }

    public function show(Request $request, PointFraicheur $pointFraicheur)
    {
        $this->ensureVisibleTo($request, $pointFraicheur);

        $pointFraicheur = $this->pointsQuery($pointFraicheur->quartier_id)->findOrFail($pointFraicheur->id);
        $avisPublies = $pointFraicheur->avis()->with('user')->where('statut', 'publie')->latest()->get();
        $monAvis = $request->user()->avis()->where('point_fraicheur_id', $pointFraicheur->id)->first();

        return view('points-fraicheur.show', compact('pointFraicheur', 'avisPublies', 'monAvis'));
    }

    public function compare(Request $request, PointCoverageService $coverage)
    {
        $data = $request->validate(['points' => ['required', 'array', 'min:2', 'max:3'], 'points.*' => ['integer']]);
        $points = $this->pointsQuery($request->user()->quartier_id)->whereIn('id', $data['points'])->get();
        abort_unless($points->count() === count(array_unique($data['points'])), 404);
        $quartier = $request->user()->quartier;
        $comparisons = $points->map(fn ($p) => ['point' => $p, 'distance' => $coverage->distance((float) $quartier->latitude, (float) $quartier->longitude, (float) $p->latitude, (float) $p->longitude)]);

        return view('points-fraicheur.compare', compact('comparisons'));
    }

    public function storeAvis(StoreAvisRequest $request, PointFraicheur $pointFraicheur)
    {
        $this->ensureVisibleTo($request, $pointFraicheur);

        if ($request->user()->avis()->where('point_fraicheur_id', $pointFraicheur->id)->exists()) {
            return back()->withErrors(['avis' => 'Vous avez déjà déposé un avis pour ce point.']);
        }

        $data = $this->avisData($request);
        $request->user()->avis()->create($data + [
            'point_fraicheur_id' => $pointFraicheur->id,
            'statut' => 'en_attente',
        ]);

        return back()->with('success', 'Votre avis a été envoyé et attend la validation de l’administration.');
    }

    public function updateAvis(StoreAvisRequest $request, PointFraicheur $pointFraicheur, Avis $avis)
    {
        $this->ensureVisibleTo($request, $pointFraicheur);
        abort_unless($avis->user_id === $request->user()->id && $avis->point_fraicheur_id === $pointFraicheur->id, 403);

        $avis->update($this->avisData($request) + ['statut' => 'en_attente']);

        return back()->with('success', 'Votre avis a été modifié et attend une nouvelle validation.');
    }

    public function destroyAvis(Request $request, PointFraicheur $pointFraicheur, Avis $avis)
    {
        $this->ensureVisibleTo($request, $pointFraicheur);
        abort_unless($avis->user_id === $request->user()->id && $avis->point_fraicheur_id === $pointFraicheur->id, 403);
        $avis->delete();

        return back()->with('success', 'Votre avis a été supprimé.');
    }

    private function pointsQuery(int $quartierId)
    {
        return PointFraicheur::query()
            ->with('quartier')
            ->where('quartier_id', $quartierId)
            ->where('actif', true)
            ->withCount(['avis as avis_publies_count' => fn ($query) => $query->where('statut', 'publie')])
            ->withAvg(['avis as note_moyenne' => fn ($query) => $query->where('statut', 'publie')], 'note');
    }

    private function ensureVisibleTo(Request $request, PointFraicheur $pointFraicheur): void
    {
        abort_unless($pointFraicheur->actif && $request->user()->quartier_id === $pointFraicheur->quartier_id, 404);
    }

    private function avisData(StoreAvisRequest $request): array
    {
        return $request->validated();
    }
}
