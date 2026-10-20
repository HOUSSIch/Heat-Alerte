<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntretienEquipement;
use App\Models\Equipement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntretienController extends Controller
{
    public function index(Request $request): View
    {
        $query = EntretienEquipement::query()
            ->with('equipement.user:id,name,email')
            ->orderByDesc('date_prevue');

        if ($request->filled('equipement_id')) {
            $query->where('equipement_id', $request->integer('equipement_id'));
        }

        if ($request->filled('user_id')) {
            $query->whereHas('equipement', fn ($q) => $q->where('user_id', $request->integer('user_id')));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_prevue', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_prevue', '<=', $request->date('date_to'));
        }

        match ($request->input('statut')) {
            'réalisé' => $query->whereNotNull('date_realisee'),
            'en_retard' => $query->whereNull('date_realisee')->whereDate('date_prevue', '<', today()),
            'prévu' => $query->whereNull('date_realisee')->whereDate('date_prevue', '>=', today()),
            default => null,
        };

        return view('admin.entretiens.index', [
            'entretiens' => $query->paginate(15)->withQueryString(),
            'equipements' => Equipement::orderBy('nom')->get(['id', 'nom', 'user_id']),
            'users' => User::orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }
}
