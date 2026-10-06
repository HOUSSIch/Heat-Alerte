@extends('layouts.admin')

@section('title', 'Dashboard administrateur · HeatAlert')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
    @php
        $statutClasses = [
            'en_attente' => 'text-bg-warning',
            'validé' => 'text-bg-primary',
            'rejeté' => 'text-bg-danger',
            'résolu' => 'text-bg-success',
        ];
        $niveauClasses = [
            'Normal' => 'text-bg-success',
            'Vigilance' => 'text-bg-warning',
            'Alerte' => 'text-bg-orange',
            'Danger' => 'text-bg-danger',
        ];
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <p class="text-secondary mb-0">Vue d'ensemble de la plateforme HeatAlert</p>
        </div>
        <span class="badge text-bg-primary px-3 py-2">Administration · {{ now()->format('d/m/Y') }}</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="small-box bg-white border shadow-sm h-100 mb-0">
                <div class="inner"><p class="text-secondary mb-1">Utilisateurs</p><h3 class="text-dark">{{ $totalUsers }}</h3></div>
                <div class="icon text-primary">@include('partials.tabler-icon', ['name' => 'user', 'class' => ''])</div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer text-primary">Voir les utilisateurs <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="small-box bg-white border shadow-sm h-100 mb-0">
                <div class="inner"><p class="text-secondary mb-1">Quartiers</p><h3 class="text-dark">{{ $totalQuartiers }}</h3></div>
                <div class="icon text-info">@include('partials.tabler-icon', ['name' => 'map-pin', 'class' => ''])</div>
                <a href="{{ route('admin.quartiers.index') }}" class="small-box-footer text-info">Voir les quartiers <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="small-box bg-white border shadow-sm h-100 mb-0">
                <div class="inner"><p class="text-secondary mb-1">Alertes actives</p><h3 class="text-dark">{{ $totalAlertesActives }}</h3></div>
                <div class="icon text-warning">@include('partials.tabler-icon', ['name' => 'alert', 'class' => ''])</div>
                <a href="{{ route('admin.alertes-meteo.index') }}" class="small-box-footer text-warning">Gérer les alertes <span aria-hidden="true">→</span></a>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="small-box bg-white border shadow-sm h-100 mb-0">
                <div class="inner"><p class="text-secondary mb-1">Coupures actives</p><h3 class="text-dark">{{ $totalCoupuresActives }}</h3></div>
                <div class="icon text-danger">@include('partials.tabler-icon', ['name' => 'bolt', 'class' => ''])</div>
                <a href="{{ route('admin.coupures.index') }}" class="small-box-footer text-danger">Gérer les coupures <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6">
            <a class="text-reset text-decoration-none" href="{{ route('admin.points-fraicheur.index') }}"><div class="info-box shadow-sm h-100 mb-0"><span class="info-box-icon text-bg-info">@include('partials.tabler-icon', ['name' => 'map-pin', 'class' => ''])</span><div class="info-box-content"><span class="info-box-text">Points de fraîcheur actifs</span><span class="info-box-number">{{ $totalPointsFraicheurActifs }}</span><span class="small text-secondary">Validés pour les habitants</span></div></div></a>
        </div>
        <div class="col-12 col-sm-6">
            <a class="text-reset text-decoration-none" href="{{ route('admin.avis.index') }}"><div class="info-box shadow-sm h-100 mb-0"><span class="info-box-icon text-bg-warning">@include('partials.tabler-icon', ['name' => 'message', 'class' => ''])</span><div class="info-box-content"><span class="info-box-text">Avis en attente</span><span class="info-box-number">{{ $totalAvisEnAttente }}</span><span class="small text-secondary">À modérer</span></div></div></a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3"><a class="text-reset text-decoration-none" href="{{ route('admin.signalements.index') }}"><div class="info-box shadow-sm h-100 mb-0"><span class="info-box-icon text-bg-warning">@include('partials.tabler-icon', ['name' => 'alert', 'class' => ''])</span><div class="info-box-content"><span class="info-box-text">Signalements en attente</span><span class="info-box-number">{{ $totalSignalementsEnAttente }}</span><span class="small text-secondary">À traiter</span></div></div></a></div>
        <div class="col-12 col-sm-6 col-xl-3"><a class="text-reset text-decoration-none" href="{{ route('admin.equipements.index') }}"><div class="info-box shadow-sm h-100 mb-0"><span class="info-box-icon text-bg-info">@include('partials.tabler-icon', ['name' => 'devices', 'class' => ''])</span><div class="info-box-content"><span class="info-box-text">Équipements</span><span class="info-box-number">{{ $totalEquipements }}</span><span class="small text-secondary">Déclarés par les habitants</span></div></div></a></div>
        <div class="col-12 col-sm-6 col-xl-3"><a class="text-reset text-decoration-none" href="{{ route('admin.conseils.index') }}"><div class="info-box shadow-sm h-100 mb-0"><span class="info-box-icon text-bg-success">@include('partials.tabler-icon', ['name' => 'bulb', 'class' => ''])</span><div class="info-box-content"><span class="info-box-text">Conseils actifs</span><span class="info-box-number">{{ $totalConseilsActifs }}</span><span class="small text-secondary">Disponibles aux habitants</span></div></div></a></div>
        <div class="col-12 col-sm-6 col-xl-3"><a class="text-reset text-decoration-none" href="{{ route('admin.users.index') }}"><div class="info-box shadow-sm h-100 mb-0"><span class="info-box-icon text-bg-primary">@include('partials.tabler-icon', ['name' => 'user', 'class' => ''])</span><div class="info-box-content"><span class="info-box-text">Habitants</span><span class="info-box-number">{{ $totalHabitants }}</span><span class="small text-secondary">{{ $totalAdmins }} administrateur{{ $totalAdmins > 1 ? 's' : '' }}</span></div></div></a></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h3 class="card-title mb-0">Accès rapides</h3></div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-6 col-xl-3"><a class="btn btn-outline-primary w-100" href="{{ route('admin.users.create') }}">@include('partials.tabler-icon', ['name' => 'user', 'class' => 'me-1']) Nouvel utilisateur</a></div>
                        <div class="col-sm-6 col-xl-3"><a class="btn btn-outline-info w-100" href="{{ route('admin.quartiers.create') }}">@include('partials.tabler-icon', ['name' => 'map-pin', 'class' => 'me-1']) Nouveau quartier</a></div>
                        <div class="col-sm-6 col-xl-3"><a class="btn btn-outline-warning w-100" href="{{ route('admin.alertes-meteo.create') }}">@include('partials.tabler-icon', ['name' => 'thermometer', 'class' => 'me-1']) Nouvelle alerte</a></div>
                        <div class="col-sm-6 col-xl-3"><a class="btn btn-outline-danger w-100" href="{{ route('admin.coupures.create') }}">@include('partials.tabler-icon', ['name' => 'bolt', 'class' => 'me-1']) Nouvelle coupure</a></div>
                        <div class="col-sm-6 col-xl-4"><a class="btn btn-light border w-100" href="{{ route('admin.signalements.index') }}">Gérer les signalements</a></div>
                        <div class="col-sm-6 col-xl-4"><a class="btn btn-light border w-100" href="{{ route('admin.equipements.index') }}">Gérer les équipements</a></div>
                        <div class="col-sm-6 col-xl-4"><a class="btn btn-light border w-100" href="{{ route('admin.conseils.index') }}">Gérer les conseils</a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h3 class="card-title mb-0">Situation HeatAlert</h3></div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><span>Alertes météo actives</span><span class="badge text-bg-warning">{{ $totalAlertesActives }}</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Coupures en cours</span><span class="badge text-bg-danger">{{ $coupuresEnCours }}</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Signalements en attente</span><span class="badge text-bg-warning">{{ $totalSignalementsEnAttente }}</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Quartiers surveillés</span><span class="badge text-bg-info">{{ $quartiersSurveilles }}</span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h3 class="card-title mb-0">Utilisateurs récents</h3><div class="card-tools"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.index') }}">Voir tous</a></div></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($recentUsers as $user)
                            <li class="list-group-item d-flex align-items-center gap-3">
                                <span class="avatar text-bg-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <div class="flex-grow-1"><a class="fw-semibold text-reset text-decoration-none" href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a><div class="small text-secondary">{{ $user->email }} · {{ $user->created_at->format('d/m/Y') }}</div></div>
                                <span class="badge {{ $user->role === 'admin' ? 'text-bg-danger' : ($user->role === 'agent' ? 'text-bg-info' : 'text-bg-primary') }}">{{ ucfirst($user->role) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-secondary">Aucun utilisateur.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h3 class="card-title mb-0">Signalements récents</h3><div class="card-tools"><a class="btn btn-sm btn-outline-warning" href="{{ route('admin.signalements.index') }}">Voir tous</a></div></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($recentSignalements as $signalement)
                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3">
                                <div><a class="fw-semibold text-reset text-decoration-none" href="{{ route('admin.signalements.show', $signalement) }}">{{ $signalement->titre }}</a><div class="small text-secondary">{{ $signalement->user?->name ?? 'Utilisateur supprimé' }} · {{ $signalement->quartier?->nom ?? 'Quartier non renseigné' }} · {{ $signalement->created_at->format('d/m/Y') }}</div></div>
                                <span class="badge {{ $statutClasses[$signalement->statut] ?? 'text-bg-secondary' }}">{{ str_replace('_', ' ', ucfirst($signalement->statut)) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-secondary">Aucun signalement.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h3 class="card-title mb-0">Alertes météo récentes</h3><div class="card-tools"><a class="btn btn-sm btn-outline-warning" href="{{ route('admin.alertes-meteo.index') }}">Voir toutes</a></div></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0"><thead><tr><th>Alerte</th><th>Quartier</th><th>Niveau</th><th>Statut</th></tr></thead><tbody>
                        @forelse ($recentAlertes as $alerte)
                            <tr><td><a class="fw-semibold text-reset text-decoration-none" href="{{ route('admin.alertes-meteo.show', $alerte) }}">{{ $alerte->titre }}</a><div class="small text-secondary">{{ $alerte->created_at->format('d/m/Y') }}</div></td><td>{{ $alerte->quartier?->nom ?? '—' }}</td><td><span class="badge {{ $niveauClasses[$alerte->niveau] ?? 'text-bg-secondary' }}">{{ $alerte->niveau }}</span></td><td><span class="badge {{ $alerte->actif ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $alerte->actif ? 'Active' : 'Inactive' }}</span></td></tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">Aucune alerte météo.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><h3 class="card-title mb-0">Équipements récents</h3><div class="card-tools"><a class="btn btn-sm btn-outline-info" href="{{ route('admin.equipements.index') }}">Voir tous</a></div></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0"><thead><tr><th>Équipement</th><th>Propriétaire</th><th>Sensibilités</th></tr></thead><tbody>
                        @forelse ($recentEquipements as $equipement)
                            <tr><td><a class="fw-semibold text-reset text-decoration-none" href="{{ route('admin.equipements.show', $equipement) }}">{{ $equipement->nom }}</a><div class="small text-secondary">{{ $equipement->type }}</div></td><td>{{ $equipement->user?->name ?? 'Utilisateur supprimé' }}</td><td>@if ($equipement->sensible_chaleur)<span class="badge text-bg-warning">Chaleur</span>@endif @if ($equipement->sensible_coupure)<span class="badge text-bg-danger">Coupure</span>@endif @if (! $equipement->sensible_chaleur && ! $equipement->sensible_coupure)<span class="text-secondary small">Aucune</span>@endif</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-secondary py-4">Aucun équipement.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
            </div>
        </div>
    </div>
@endsection
