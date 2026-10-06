<?php

namespace App\Http\Controllers;

use App\Models\CoupureElectrique;
use Illuminate\Http\Request;

class CoupureController extends Controller
{
    public function index(Request $request)
    {
        $quartierId = $request->user()->quartier_id;
        $coupuresQuery = CoupureElectrique::query()
            ->where('actif', true)
            ->when($quartierId, fn ($query) => $query->where('quartier_id', $quartierId));

        return view('coupures.index', [
            'coupures' => (clone $coupuresQuery)->with('quartier')->latest()->get(),
            'coupuresEnCours' => (clone $coupuresQuery)->where('statut', 'en_cours')->count(),
            'coupuresPrevues' => (clone $coupuresQuery)->where('statut', 'prévue')->count(),
            'signalementsEnAttente' => $request->user()->signalements()->where('statut', 'en_attente')->count(),
        ]);
    }

    public function show(CoupureElectrique $coupure)
    {
        return view('coupures.show', [
            'coupure' => $coupure->load('quartier')->loadCount('signalements'),
        ]);
    }
}
