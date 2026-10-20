<?php

namespace App\Http\Controllers;

use App\Models\CoupureElectrique;
use App\Services\CoupureTrackingService;
use Illuminate\Http\Request;

class CoupureController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->fresh();
        $quartierId = $user->quartier_id;
        $coupuresQuery = CoupureElectrique::query()->where('actif', true);

        // A resident without a quartier must never receive every active outage.
        if ($quartierId) {
            $coupuresQuery->where('quartier_id', $quartierId);
        } else {
            $coupuresQuery->whereRaw('1 = 0');
        }

        return view('coupures.index', [
            'coupures' => (clone $coupuresQuery)->with('quartier')->latest()->get(),
            'coupuresEnCours' => (clone $coupuresQuery)->where('statut', 'en_cours')->count(),
            'coupuresPrevues' => (clone $coupuresQuery)->where('statut', 'prévue')->count(),
            'signalementsEnAttente' => $user->signalements()->where('statut', 'en_attente')->count(),
            'sansQuartier' => is_null($quartierId),
        ]);
    }

    public function show(Request $request, CoupureElectrique $coupure)
    {
        $user = $request->user()->fresh();

        abort_unless(
            $coupure->actif && $user->quartier_id && $coupure->quartier_id === $user->quartier_id,
            404
        );

        return view('coupures.show', [
            'coupure' => $coupure->load('quartier')->loadCount('signalements'),
            'tracking' => app(CoupureTrackingService::class)->track($coupure),
        ]);
    }

    public function planning(Request $request)
    {
        $quartierId = $request->user()->quartier_id;
        abort_unless($quartierId, 404);
        $status = $request->string('statut')->toString();
        $coupures = CoupureElectrique::where('actif', true)->where('quartier_id', $quartierId)->whereNotNull('date_debut')->get()
            ->filter(function (CoupureElectrique $coupure) use ($status): bool {
                $tracking = app(CoupureTrackingService::class)->track($coupure);

                return $status === '' || $tracking['status'] === $status;
            })->values();

        $events = $coupures->map(fn (CoupureElectrique $coupure) => [
            'title' => $coupure->titre,
            'start' => $coupure->date_debut?->toIso8601String(),
            'end' => $coupure->date_fin_estimee?->toIso8601String(),
            'url' => route('coupures.show', $coupure),
        ])->all();

        return view('coupures.planning', compact('coupures', 'status', 'events'));
    }
}
