<?php

namespace App\Http\Controllers;

use App\Models\AlerteMeteo;
use App\Models\EtablissementSensible;
use App\Services\HeatAlertAiService;
use App\Services\WeatherVigilanceService;
use Illuminate\Http\Request;

class MeteoController extends Controller
{
    public function index(Request $r, WeatherVigilanceService $vigilance)
    {
        $quartier = $r->user()->quartier;
        if (! $quartier) {
            return view('meteo.index', ['quartier' => null, 'alertesActives' => collect()]);
        }$alertesActives = AlerteMeteo::where('quartier_id', $quartier->id)->where('actif', true)->where(fn ($q) => $q->whereNull('date_debut')->orWhere('date_debut', '<=', now()))->where(fn ($q) => $q->whereNull('date_fin')->orWhere('date_fin', '>=', now()))->latest()->get();
        try {
            $weather = $vigilance->forQuartier($quartier);
            $niveau = $weather['niveau'];

            return view('meteo.index', compact('quartier', 'weather', 'niveau', 'alertesActives'));
        } catch (\RuntimeException $e) {
            return view('meteo.index', compact('quartier', 'alertesActives'))->withErrors(['meteo' => $e->getMessage()]);
        }
    }

    public function brief(Request $request, WeatherVigilanceService $vigilance, HeatAlertAiService $ai)
    {
        $quartier = $request->user()->quartier;
        abort_unless($quartier, 404);
        try {
            $weather = $vigilance->forQuartier($quartier);
            $context = [
                'quartier' => $quartier->only(['nom', 'ville']),
                'prevision' => $weather['days'],
                'niveau' => $weather['niveau'],
                'alertes' => AlerteMeteo::where('quartier_id', $quartier->id)->where('actif', true)->get(['titre', 'niveau', 'description']),
                'structures' => EtablissementSensible::where('quartier_id', $quartier->id)->where('actif', true)->get(['nom', 'type', 'horaires', 'services', 'accueil_public']),
            ];
            $brief = $ai->ask('Tu es HeatAlert. Réponds en français avec Situation, Risques, Recommandations et Structures utiles. N’invente aucune donnée. Contexte : '.json_encode($context, JSON_UNESCAPED_UNICODE));
        } catch (\Throwable) {
            $brief = 'Le brief IA est temporairement indisponible.';
        }

        return redirect()->route('meteo.index')->with('quartier_brief', $brief);
    }
}
