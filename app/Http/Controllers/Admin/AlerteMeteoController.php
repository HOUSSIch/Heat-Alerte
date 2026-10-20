<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlerteMeteo;
use App\Models\Quartier;
use Illuminate\Http\Request;

class AlerteMeteoController extends Controller
{
    private function data(Request $r): array
    {
        $d = $r->validate([
            'quartier_id' => ['required', 'exists:quartiers,id'],
            'titre' => ['bail', 'required', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'temperature' => ['nullable', 'numeric', 'between:-50,70'],
            'temperature_max' => ['nullable', 'numeric', 'between:-50,70', 'gte:temperature_min'],
            'temperature_min' => ['nullable', 'numeric', 'between:-50,70', 'lte:temperature_max'],
            'niveau' => ['required', 'in:Normal,Vigilance,Alerte,Critique,Danger'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'actif' => ['nullable', 'boolean'],
        ]);
        $d['actif'] = $r->boolean('actif');
        $d['source'] = 'manuel';

        return $d;
    }

    public function index()
    {
        return view('admin.alertes-meteo.index', ['alertes' => AlerteMeteo::with('quartier')->latest()->get()]);
    }

    public function create()
    {
        return view('admin.alertes-meteo.form', ['alerte' => new AlerteMeteo, 'quartiers' => Quartier::where('actif', true)->get()]);
    }

    public function store(Request $r)
    {
        AlerteMeteo::create($this->data($r));

        return redirect()->route('admin.alertes-meteo.index')->with('success', 'Alerte créée.');
    }

    public function show(AlerteMeteo $alerte_meteo)
    {
        return view('admin.alertes-meteo.show', ['alerte' => $alerte_meteo->load('quartier')]);
    }

    public function edit(AlerteMeteo $alerte_meteo)
    {
        return view('admin.alertes-meteo.form', ['alerte' => $alerte_meteo, 'quartiers' => Quartier::where('actif', true)->get()]);
    }

    public function update(Request $r, AlerteMeteo $alerte_meteo)
    {
        $alerte_meteo->update($this->data($r));

        return redirect()->route('admin.alertes-meteo.show', $alerte_meteo)->with('success', 'Alerte modifiée.');
    }

    public function destroy(AlerteMeteo $alerte_meteo)
    {
        $alerte_meteo->delete();

        return redirect()->route('admin.alertes-meteo.index')->with('success', 'Alerte supprimée.');
    }
}
