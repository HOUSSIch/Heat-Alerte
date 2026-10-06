<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\PointFraicheur;
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

    public function storeAvis(Request $request, PointFraicheur $pointFraicheur)
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

    public function updateAvis(Request $request, PointFraicheur $pointFraicheur, Avis $avis)
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

    private function avisData(Request $request): array
    {
        return $request->validate([
            'note' => ['required', 'integer', 'min:1', 'max:5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
