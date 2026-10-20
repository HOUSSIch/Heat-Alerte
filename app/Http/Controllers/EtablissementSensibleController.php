<?php

namespace App\Http\Controllers;

use App\Models\EtablissementSensible;
use Illuminate\Http\Request;

class EtablissementSensibleController extends Controller
{
    public function index(Request $request)
    {
        $quartier = $request->user()->quartier;
        $etablissements = collect();

        if ($quartier) {
            $etablissements = EtablissementSensible::query()
                ->with('quartier')
                ->where('quartier_id', $quartier->id)
                ->where('actif', true)
                ->latest()
                ->get();
        }

        return view('etablissements-sensibles.index', compact('quartier', 'etablissements'));
    }

    public function show(Request $request, EtablissementSensible $etablissementSensible)
    {
        $etablissementSensible = EtablissementSensible::query()
            ->with('quartier')
            ->whereKey($etablissementSensible->id)
            ->where('quartier_id', $request->user()->quartier_id)
            ->where('actif', true)
            ->firstOrFail();

        return view('etablissements-sensibles.show', compact('etablissementSensible'));
    }
}
