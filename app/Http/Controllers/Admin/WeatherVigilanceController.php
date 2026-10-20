<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quartier;
use App\Services\WeatherVigilanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeatherVigilanceController extends Controller
{
    public function index(Request $request, WeatherVigilanceService $service): View
    {
        $quartiers = Quartier::query()->withCount('users')->where('actif', true)->orderBy('nom')->get();
        $rows = $quartiers->map(function (Quartier $quartier) use ($service) {
            try {
                return ['quartier' => $quartier, 'vigilance' => $service->forQuartier($quartier)];
            } catch (\RuntimeException) {
                return ['quartier' => $quartier, 'vigilance' => null];
            }
        })->filter(function (array $row) use ($request) {
            return ! $request->filled('niveau') || data_get($row, 'vigilance.niveau') === $request->input('niveau');
        });

        return view('admin.vigilance-meteo.index', compact('rows'));
    }

    public function refresh(Quartier $quartier, WeatherVigilanceService $service): RedirectResponse
    {
        try {
            $vigilance = $service->forQuartier($quartier, true);
            $service->updateAlert($quartier, $vigilance);

            return back()->with('success', 'Données météo actualisées.');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['meteo' => $e->getMessage()]);
        }
    }
}
