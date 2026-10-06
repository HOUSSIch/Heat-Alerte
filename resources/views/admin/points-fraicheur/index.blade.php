@extends('layouts.admin')

@section('title', 'Points de fraîcheur')
@section('page-title', 'Points de fraîcheur')
@section('breadcrumb')<li class="breadcrumb-item active">Points de fraîcheur</li>@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <div><h3 class="card-title mb-0">Points officiels HeatAlert</h3><div class="small text-secondary">Lieux validés par l’administration pour les fortes chaleurs.</div></div>
            <div class="card-tools d-flex gap-2"><a class="btn btn-outline-info btn-sm" href="{{ route('admin.points-fraicheur.geoapify') }}">Rechercher avec Geoapify</a><a class="btn btn-primary btn-sm" href="{{ route('admin.points-fraicheur.create') }}">Créer un point</a></div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Nom</th><th>Quartier</th><th>Type</th><th>Source</th><th>Avis</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($points as $point)
                        <tr>
                            <td><a class="fw-semibold text-reset text-decoration-none" href="{{ route('admin.points-fraicheur.show', $point) }}">{{ $point->nom }}</a><div class="small text-secondary">{{ $point->adresse }}</div></td>
                            <td>{{ $point->quartier?->nom ?? '—' }}</td><td>{{ $point->type }}</td>
                            <td><span class="badge {{ $point->source === 'geoapify' ? 'text-bg-info' : 'text-bg-secondary' }}">{{ $point->source === 'geoapify' ? 'GEOAPIFY' : 'Manuel' }}</span></td>
                            <td>{{ $point->avis_count }}</td><td><span class="badge {{ $point->actif ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $point->actif ? 'Actif' : 'Inactif' }}</span></td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.points-fraicheur.show', $point) }}">Voir</a><a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.points-fraicheur.edit', $point) }}">Modifier</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-secondary py-5">Aucun point de fraîcheur enregistré. Lancez une recherche Geoapify ou créez un point manuellement.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
