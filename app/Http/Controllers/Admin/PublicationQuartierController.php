<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicationQuartier;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicationQuartierController extends Controller
{
    public function index(Request $request): View
    {
        $query = PublicationQuartier::with(['quartier', 'user'])->withCount(['commentaires', 'confirmations'])->latest();

        foreach (['quartier_id' => 'quartier_id', 'user_id' => 'user_id'] as $input => $column) {
            if ($request->filled($input)) {
                $query->where($column, $request->integer($input));
            }
        }
        foreach (['type' => ['probleme', 'information', 'entraide', 'alerte'], 'statut' => ['signale', 'en_cours', 'resolu']] as $input => $allowed) {
            if (in_array($request->string($input)->toString(), $allowed, true)) {
                $query->where($input, $request->string($input)->toString());
            }
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date('date'));
        }

        return view('admin.discussions-quartier.index', [
            'publications' => $query->get(),
            'quartiers' => Quartier::orderBy('nom')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function show(PublicationQuartier $publication): View
    {
        return view('admin.discussions-quartier.show', ['publication' => $publication->load(['quartier', 'user', 'commentaires.user'])->loadCount(['commentaires', 'confirmations'])]);
    }

    public function update(Request $request, PublicationQuartier $publication): RedirectResponse
    {
        $data = $request->validate(['statut' => ['required', 'in:signale,en_cours,resolu']]);
        $publication->update(['statut' => $data['statut'], 'resolved_at' => $data['statut'] === 'resolu' ? now() : null]);

        return back()->with('success', 'Statut de la discussion mis à jour.');
    }

    public function destroy(PublicationQuartier $publication): RedirectResponse
    {
        $publication->delete();

        return redirect()->route('admin.discussions-quartier.index')->with('success', 'Discussion supprimée.');
    }
}
