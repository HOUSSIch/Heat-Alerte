<?php

namespace App\Http\Controllers;

use App\Models\Conseil;
use App\Models\Equipement;
use Illuminate\Http\Request;

class ConseilController extends Controller
{
    public function index()
    {
        return view('conseils.index', ['conseils' => Conseil::withCount('equipements')->latest()->get()]);
    }

    public function create()
    {
        return view('conseils.create', ['equipements' => Equipement::orderBy('nom')->get()]);
    }

    public function store(Request $r)
    {
        $data = $this->data($r);
        $equipementIds = $data['equipements'] ?? [];
        unset($data['equipements']);
        $c = Conseil::create($data);
        $c->equipements()->sync($equipementIds);

        return redirect()->route('conseils.index')->with('success', 'Conseil ajouté.');
    }

    public function show(Conseil $conseil)
    {
        return view('conseils.show', ['conseil' => $conseil->load('equipements')]);
    }

    public function edit(Conseil $conseil)
    {
        return view('conseils.edit', ['conseil' => $conseil->load('equipements'), 'equipements' => Equipement::orderBy('nom')->get()]);
    }

    public function update(Request $r, Conseil $conseil)
    {
        $data = $this->data($r);
        $equipementIds = $data['equipements'] ?? [];
        unset($data['equipements']);
        $conseil->update($data);
        $conseil->equipements()->sync($equipementIds);

        return redirect()->route('conseils.show', $conseil)->with('success', 'Conseil modifié.');
    }

    public function destroy(Conseil $conseil)
    {
        $conseil->delete();

        return redirect()->route('conseils.index')->with('success', 'Conseil supprimé.');
    }

    public function mes(Request $r)
    {
        $conseils = Conseil::query()->where('actif', true)->whereHas('equipements', fn ($q) => $q->where('user_id', $r->user()->id))->with(['equipements' => fn ($q) => $q->where('user_id', $r->user()->id)])->distinct()->get();

        return view('conseils.mes', compact('conseils'));
    }

    private function data(Request $r): array
    {
        $d = $r->validate([
            'titre' => ['bail', 'required', 'string', 'min:3', 'max:150'],
            'description' => ['bail', 'required', 'string', 'min:10', 'max:5000'],
            'categorie' => ['required', 'in:Chaleur,Coupure électrique,Hydratation,Protection appareil,Sécurité,Économie d’énergie,Autre'],
            'niveau' => ['required', 'in:Information,Important,Urgent'],
            'actif' => ['nullable', 'boolean'],
            'equipements' => ['nullable', 'array'],
            'equipements.*' => ['integer', 'distinct', 'exists:equipements,id'],
        ]);
        $d['actif'] = $r->boolean('actif');

        return $d;
    }
}
