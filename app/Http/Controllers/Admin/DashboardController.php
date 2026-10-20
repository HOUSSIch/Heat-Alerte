<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlerteMeteo;
use App\Models\Avis;
use App\Models\Conseil;
use App\Models\CoupureElectrique;
use App\Models\EntretienEquipement;
use App\Models\Equipement;
use App\Models\PointFraicheur;
use App\Models\Quartier;
use App\Models\Signalement;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $alertesActives = AlerteMeteo::query()
            ->where('actif', true)
            ->where(fn ($query) => $query->whereNull('date_debut')->orWhere('date_debut', '<=', now()))
            ->where(fn ($query) => $query->whereNull('date_fin')->orWhere('date_fin', '>=', now()));

        $coupuresActives = CoupureElectrique::query()
            ->where('actif', true)
            ->whereIn('statut', ['prévue', 'en_cours']);

        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalHabitants' => User::where('role', 'habitant')->count(),
            'totalAdmins' => User::where('role', 'admin')->count(),
            'totalQuartiers' => Quartier::count(),
            'totalAlertesActives' => $alertesActives->count(),
            'quartiersEnVigilance' => (clone $alertesActives)->where('niveau', 'Vigilance')->count(),
            'quartiersEnAlerte' => (clone $alertesActives)->where('niveau', 'Alerte')->count(),
            'quartiersCritiques' => (clone $alertesActives)->whereIn('niveau', ['Critique', 'Danger'])->count(),
            'temperatureMaxPrevue' => (clone $alertesActives)->max('temperature_max'),
            'totalCoupuresActives' => $coupuresActives->count(),
            'totalCoupuresPrevues' => CoupureElectrique::where('actif', true)->where('statut', 'prévue')->count(),
            'totalCoupuresEnRetard' => CoupureElectrique::where('actif', true)->whereNull('date_fin_reelle')->whereNotNull('date_fin_estimee')->where('date_fin_estimee', '<', now())->count(),
            'totalSignalementsEnAttente' => Signalement::where('statut', 'en_attente')->count(),
            'totalSignalementsResolus' => Signalement::where('statut', 'résolu')->count(),
            'totalEquipements' => Equipement::count(),
            'totalEquipementsSensiblesChaleur' => Equipement::where('sensible_chaleur', true)->count(),
            'totalEquipementsSensiblesCoupure' => Equipement::where('sensible_coupure', true)->count(),
            'totalEntretiensEnRetard' => EntretienEquipement::whereNull('date_realisee')->whereDate('date_prevue', '<', today())->count(),
            'totalEntretiensCetteSemaine' => EntretienEquipement::whereNull('date_realisee')->whereBetween('date_prevue', [today(), today()->copy()->endOfWeek()])->count(),
            'totalConseilsActifs' => Conseil::where('actif', true)->count(),
            'totalPointsFraicheurActifs' => PointFraicheur::where('actif', true)->count(),
            'totalAvisEnAttente' => Avis::where('statut', 'en_attente')->count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentSignalements' => Signalement::with(['user', 'quartier'])->latest()->take(5)->get(),
            'recentAlertes' => AlerteMeteo::with('quartier')->latest()->take(5)->get(),
            'recentEquipements' => Equipement::with('user')->latest()->take(5)->get(),
            'coupuresEnCours' => (clone $coupuresActives)->where('statut', 'en_cours')->count(),
            'quartiersSurveilles' => Quartier::where('actif', true)->count(),
        ]);
    }
}
