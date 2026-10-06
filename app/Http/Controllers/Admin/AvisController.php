<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    public function index(Request $request)
    {
        $statut = $request->string('statut')->toString();
        $query = Avis::with(['user', 'pointFraicheur.quartier'])->latest();

        if (in_array($statut, ['en_attente', 'publie', 'rejete'], true)) {
            $query->where('statut', $statut);
        }

        return view('admin.avis.index', ['avis' => $query->get(), 'statut' => $statut]);
    }

    public function show(Avis $avis)
    {
        return view('admin.avis.show', ['avis' => $avis->load(['user', 'pointFraicheur.quartier'])]);
    }

    public function publish(Avis $avis)
    {
        $avis->update(['statut' => 'publie']);

        return back()->with('success', 'Avis publié.');
    }

    public function reject(Avis $avis)
    {
        $avis->update(['statut' => 'rejete']);

        return back()->with('success', 'Avis rejeté.');
    }

    public function destroy(Avis $avis)
    {
        $avis->delete();

        return redirect()->route('admin.avis.index')->with('success', 'Avis supprimé.');
    }
}
