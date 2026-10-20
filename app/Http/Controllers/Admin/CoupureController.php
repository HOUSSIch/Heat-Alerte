<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoupureElectrique;
use App\Models\Quartier;
use App\Services\CoupureTrackingService;
use Illuminate\Http\Request;

class CoupureController extends Controller
{
    private function data(Request $request): array
    {
        $data = $request->validate([
            'quartier_id' => ['required', 'exists:quartiers,id'],
            'titre' => ['bail', 'required', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', 'in:panne,maintenance,délestage'],
            'statut' => ['required', 'in:prévue,en_cours,terminée'],
            'cause' => ['nullable', 'string', 'max:1000'],
            'date_debut' => ['nullable', 'date'],
            'date_fin_estimee' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'date_fin_reelle' => ['nullable', 'date', 'after_or_equal:date_debut'],
        ]);

        $quartier = Quartier::findOrFail($data['quartier_id']);
        $data['latitude'] = $quartier->latitude;
        $data['longitude'] = $quartier->longitude;
        $data['adresse'] = trim($quartier->nom.', '.$quartier->ville);

        return $data + [
            'source' => 'admin',
            'actif' => $request->boolean('actif'),
        ];
    }

    public function index()
    {
        return view('admin.coupures.index', ['coupures' => CoupureElectrique::with('quartier')->latest()->get()]);
    }

    public function create()
    {
        return view('admin.coupures.form', ['coupure' => new CoupureElectrique, 'quartiers' => Quartier::orderBy('nom')->get()]);
    }

    public function store(Request $request)
    {
        CoupureElectrique::create($this->data($request));

        return redirect()->route('admin.coupures.index');
    }

    public function show(CoupureElectrique $coupure)
    {
        return view('admin.coupures.show', ['coupure' => $coupure->load('quartier')->loadCount('signalements'), 'tracking' => app(CoupureTrackingService::class)->track($coupure)]);
    }

    public function edit(CoupureElectrique $coupure)
    {
        return view('admin.coupures.form', ['coupure' => $coupure, 'quartiers' => Quartier::orderBy('nom')->get()]);
    }

    public function update(Request $request, CoupureElectrique $coupure)
    {
        $coupure->update($this->data($request));

        return redirect()->route('admin.coupures.show', $coupure);
    }

    public function destroy(CoupureElectrique $coupure)
    {
        $coupure->delete();

        return redirect()->route('admin.coupures.index');
    }

    public function planning(Request $request)
    {
        $query = CoupureElectrique::with('quartier')->whereNotNull('date_debut');
        if ($request->filled('quartier_id')) {
            $query->where('quartier_id', $request->integer('quartier_id'));
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $coupures = $query->get();
        $events = $coupures->map(fn (CoupureElectrique $coupure) => [
            'title' => $coupure->titre.' — '.$coupure->quartier?->nom,
            'start' => $coupure->date_debut?->toIso8601String(),
            'end' => $coupure->date_fin_estimee?->toIso8601String(),
            'url' => route('admin.coupures.show', $coupure),
        ])->all();

        return view('admin.coupures.planning', compact('coupures', 'events') + ['quartiers' => Quartier::orderBy('nom')->get()]);
    }
}
