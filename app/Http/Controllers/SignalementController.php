<?php

namespace App\Http\Controllers;

use App\Models\Quartier;
use App\Models\Signalement;
use App\Services\GroqAiService;
use App\Services\NominatimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SignalementController extends Controller
{
    public function index(Request $r)
    {
        return view('signalements.index', ['signalements' => $r->user()->signalements()->with(['quartier', 'coupureElectrique'])->latest()->get()]);
    }

    public function create(Request $r)
    {
        return view('signalements.form', ['quartiers' => Quartier::where('actif', true)->get(), 'signalement' => new Signalement(['quartier_id' => $r->user()->quartier_id])]);
    }

    public function show(Request $r, Signalement $signalement)
    {
        abort_unless($signalement->user_id === $r->user()->id, 404);

        return view('signalements.show', compact('signalement'));
    }

    public function store(Request $r, NominatimService $geo)
    {
        $d = $r->validate(['titre' => ['bail', 'required', 'string', 'min:5', 'max:150'], 'description' => ['bail', 'required', 'string', 'min:10', 'max:5000'], 'adresse' => ['nullable', 'string', 'max:255']]);
        $q = $r->user()->quartier;
        abort_unless($q, 422, 'Veuillez sélectionner un quartier dans votre profil.');
        $query = ! empty($d['adresse']) ? trim($d['adresse'].', '.$q->nom.', '.$q->ville.', Tunisia') : null;
        $coords = $query ? $geo->geocode($query) : null;
        $signalement = $r->user()->signalements()->create($d + ['quartier_id' => $q->id, 'latitude' => $coords['latitude'] ?? null, 'longitude' => $coords['longitude'] ?? null, 'statut' => 'en_attente', 'date_signalement' => now()]);

        return redirect()->route('signalements.show', $signalement)->with('success', $coords ? 'Signalement enregistré et localisation trouvée avec succès.' : 'Signalement enregistré, mais l’adresse n’a pas pu être localisée.');
    }

    public function addressSearch(Request $request, NominatimService $geo)
    {
        $data = $request->validate(['q' => ['required', 'string', 'min:3', 'max:255']]);

        try {
            return response()->json(['results' => $geo->search($data['q'])]);
        } catch (\RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }
    }

    public function assistantFill(Request $request, GroqAiService $ai)
    {
        $data = $request->validate(['texte' => ['bail', 'required', 'string', 'min:3', 'max:2000']]);
        $started = microtime(true);

        Log::info('[AI-SIGNAL] Controller entered', [
            'user_id' => $request->user()->id,
            'text_length' => mb_strlen($data['texte']),
        ]);
        Log::info('Signalement AI started', [
            'user_id' => $request->user()->id,
            'text_length' => mb_strlen($data['texte']),
            'model' => config('services.groq.model', 'openai/gpt-oss-20b'),
        ]);

        try {
            Log::info('[AI-SIGNAL] Calling Groq');
            $groqStarted = microtime(true);
            $proposal = $ai->parseSignalement($data['texte']);
            Log::info('[AI-SIGNAL] Groq returned', [
                'duration_ms' => (int) ((microtime(true) - $groqStarted) * 1000),
            ]);
            $quartier = filled($proposal['quartier'])
                ? Quartier::where('actif', true)
                    ->whereRaw('LOWER(nom) = ?', [mb_strtolower($proposal['quartier'])])
                    ->first()
                : null;
            $quartier ??= $request->user()->quartier;

            Log::info('Signalement AI finished', [
                'user_id' => $request->user()->id,
                'total_duration_ms' => (int) ((microtime(true) - $started) * 1000),
            ]);
            Log::info('[AI-SIGNAL] Response fields', [
                'has_titre' => isset($proposal['titre']),
                'has_description' => isset($proposal['description']),
                'has_adresse' => isset($proposal['adresse']),
                'has_quartier' => isset($quartier),
            ]);
            Log::info('[AI-SIGNAL] Returning response to browser');

            return response()->json([
                'titre' => $proposal['titre'], 'description' => $proposal['description'], 'adresse' => $proposal['adresse'],
                'quartier_id' => $quartier?->id,
            ]);
        } catch (\RuntimeException $exception) {
            $message = $exception->getMessage();
            $status = str_contains($message, 'quota') ? 429 : (str_contains($message, 'trop de temps') ? 504 : 422);

            return response()->json(['message' => $message], $status);
        } catch (\Throwable) {
            return response()->json(['message' => 'Assistant HeatAlert temporairement indisponible.'], 503);
        }
    }
}
