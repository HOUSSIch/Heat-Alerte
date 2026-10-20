<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\PointFraicheur;
use App\Services\GroqAiService;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    public function index(Request $request)
    {
        $statut = $request->string('statut')->toString();
        $query = Avis::with(['user', 'pointFraicheur.quartier'])->latest();

        if (in_array($statut, ['en_attente', 'publie', 'rejete'], true)) {
            $query->where('statut', $statut);
        }

        return view('admin.avis.index', ['avis' => $query->get(), 'statut' => $statut, 'pointsAnalysables' => PointFraicheur::whereHas('avis', fn ($query) => $query->where('statut', 'publie'))->orderBy('nom')->get()]);
    }

    public function show(Avis $avis)
    {
        return view('admin.avis.show', ['avis' => $avis->load(['user', 'pointFraicheur.quartier'])]);
    }

    public function publish(Avis $avis)
    {
        $avis->update(['statut' => 'publie']);

        return back()->with('success', 'Avis publié.');
    }

    public function reject(Avis $avis)
    {
        $avis->update(['statut' => 'rejete']);

        return back()->with('success', 'Avis rejeté.');
    }

    public function destroy(Avis $avis)
    {
        $avis->delete();

        return redirect()->route('admin.avis.index')->with('success', 'Avis supprimé.');
    }

    public function analyse(Request $request, GroqAiService $groq)
    {
        $data = $request->validate(['point_fraicheur_id' => ['required', 'exists:points_fraicheur,id']]);
        $point = PointFraicheur::findOrFail($data['point_fraicheur_id']);

        try {
            $analysis = $groq->analyse($point->id, $point->avis()->where('statut', 'publie')->latest()->get());
            if ($request->expectsJson()) {
                return response()->json(['analysis' => $analysis, 'point' => $point->nom]);
            }

            return back()->with('avis_analysis', $analysis)->with('analysed_point', $point->nom);
        } catch (\RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage()], 422);
            }

            return back()->with('error', $exception->getMessage());
        }
    }
}
