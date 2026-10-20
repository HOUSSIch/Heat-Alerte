<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Equipement::query()
            ->with(['user:id,name,email', 'entretiens'])
            ->withCount('entretiens')
            ->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        foreach (['sensible_chaleur', 'sensible_coupure'] as $sensitivity) {
            if ($request->filled($sensitivity)) {
                $query->where($sensitivity, $request->boolean($sensitivity));
            }
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();
            $query->where(fn ($q) => $q
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('marque', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($user) => $user
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")));
        }

        match ($request->input('statut_entretien')) {
            'en_retard' => $query->whereHas('entretiens', fn ($q) => $q->whereNull('date_realisee')->whereDate('date_prevue', '<', today())),
            'prévu' => $query->whereHas('entretiens', fn ($q) => $q->whereNull('date_realisee')->whereDate('date_prevue', '>=', today())),
            'aucun_entretien' => $query->whereDoesntHave('entretiens'),
            'à_jour' => $query->whereHas('entretiens')->whereDoesntHave('entretiens', fn ($q) => $q->whereNull('date_realisee')->whereDate('date_prevue', '<', today()))->whereDoesntHave('entretiens', fn ($q) => $q->whereNull('date_realisee')->whereDate('date_prevue', '>=', today())),
            default => null,
        };

        return view('admin.equipements.index', [
            'equipements' => $query->paginate(15)->withQueryString(),
            'users' => User::orderBy('name')->get(['id', 'name', 'email']),
            'types' => Equipement::query()->whereNotNull('type')->distinct()->orderBy('type')->pluck('type'),
        ]);
    }

    public function show(Equipement $equipement): View
    {
        return view('admin.equipements.show', [
            'equipement' => $equipement->load([
                'user:id,name,email',
                'conseils',
                'entretiens' => fn ($query) => $query->orderByDesc('date_realisee')->orderBy('date_prevue'),
            ]),
        ]);
    }
}
