@extends('layouts.user')

@section('title', $coupure->titre)

@section('content')
    @php
        $statutClasses = [
            'prévue' => 'bg-blue-lt text-blue',
            'en_cours' => 'bg-orange-lt text-orange',
            'terminée' => 'bg-green-lt text-green',
            'annulée' => 'bg-secondary-lt text-secondary',
        ];
        $statutClass = $statutClasses[$coupure->statut] ?? 'bg-secondary-lt text-secondary';
        $hasCoordinates = ! is_null($coupure->latitude) && ! is_null($coupure->longitude);
    @endphp

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Détail de la coupure</div>
                <h2 class="page-title">{{ $coupure->titre }}</h2>
            </div>
            <div class="col-auto d-flex flex-wrap align-items-center gap-2 mt-3 mt-sm-0">
                <span class="badge {{ $statutClass }} fs-6">{{ str_replace('_', ' ', ucfirst($coupure->statut)) }}</span>
                <a class="btn btn-outline-primary" href="{{ route('coupures.index') }}">Retour aux coupures</a>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Informations sur la coupure</h3></div>
                <div class="card-body">
                    @if ($coupure->description)
                        <p class="text-secondary mb-4">{{ $coupure->description }}</p>
                    @else
                        <p class="text-secondary mb-4">Aucune description complémentaire n’a été communiquée.</p>
                    @endif

                    <div class="row row-cards">
                        <div class="col-md-6"><div class="d-flex gap-3"><span class="avatar bg-blue-lt text-blue">@include('partials.tabler-icon', ['name' => 'map-pin'])</span><div><div class="text-secondary small">Quartier</div><div class="fw-semibold">{{ $coupure->quartier?->nom ?? 'Non renseigné' }}</div></div></div></div>
                        <div class="col-md-6"><div class="d-flex gap-3"><span class="avatar bg-orange-lt text-orange">@include('partials.tabler-icon', ['name' => 'bolt'])</span><div><div class="text-secondary small">Type</div><div class="fw-semibold">{{ $coupure->type ?: 'Non renseigné' }}</div></div></div></div>
                        <div class="col-md-6"><div class="d-flex gap-3"><span class="avatar bg-yellow-lt text-yellow">@include('partials.tabler-icon', ['name' => 'alert'])</span><div><div class="text-secondary small">Cause</div><div class="fw-semibold">{{ $coupure->cause ?: 'Non renseignée' }}</div></div></div></div>
                        <div class="col-md-6"><div class="d-flex gap-3"><span class="avatar bg-secondary-lt text-secondary">@include('partials.tabler-icon', ['name' => 'map-pin'])</span><div><div class="text-secondary small">Adresse</div><div class="fw-semibold">{{ $coupure->adresse ?: 'Non renseignée' }}</div></div></div></div>
                        <div class="col-md-6"><div class="d-flex gap-3"><span class="avatar bg-blue-lt text-blue">@include('partials.tabler-icon', ['name' => 'alert'])</span><div><div class="text-secondary small">Début</div><div class="fw-semibold">{{ $coupure->date_debut?->format('d/m/Y H:i') ?? 'À confirmer' }}</div></div></div></div>
                        <div class="col-md-6"><div class="d-flex gap-3"><span class="avatar bg-blue-lt text-blue">@include('partials.tabler-icon', ['name' => 'alert'])</span><div><div class="text-secondary small">Fin estimée</div><div class="fw-semibold">{{ $coupure->date_fin_estimee?->format('d/m/Y H:i') ?? 'À confirmer' }}</div></div></div></div>
                        @if ($coupure->date_fin_reelle)
                            <div class="col-md-6"><div class="d-flex gap-3"><span class="avatar bg-green-lt text-green">@include('partials.tabler-icon', ['name' => 'alert'])</span><div><div class="text-secondary small">Fin réelle</div><div class="fw-semibold">{{ $coupure->date_fin_reelle->format('d/m/Y H:i') }}</div></div></div></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Localisation <span class="badge bg-blue-lt text-blue ms-1">OpenStreetMap</span></h3></div>
                <div class="card-body">
                    @if ($hasCoordinates)
                        <div class="row mb-3 small text-secondary">
                            <div class="col-md-4"><strong class="text-body">Adresse</strong><br>{{ $coupure->adresse ?: 'Non renseignée' }}</div>
                            <div class="col-md-4"><strong class="text-body">Latitude</strong><br>{{ $coupure->latitude }}</div>
                            <div class="col-md-4"><strong class="text-body">Longitude</strong><br>{{ $coupure->longitude }}</div>
                        </div>
                        <div id="coupure-map" class="rounded border" style="height: 340px;"></div>
                        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
                        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                        <script>
                            const coupureMap = L.map('coupure-map').setView([@json($coupure->latitude), @json($coupure->longitude)], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(coupureMap);
                            L.marker([@json($coupure->latitude), @json($coupure->longitude)]).addTo(coupureMap).bindPopup(@json($coupure->titre));
                        </script>
                    @else
                        <div class="alert alert-warning mb-0">Localisation non disponible.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Statut</h3></div>
                <div class="card-body">
                    <span class="badge {{ $statutClass }} fs-5">{{ str_replace('_', ' ', ucfirst($coupure->statut)) }}</span>
                    <div class="mt-3"><div class="text-secondary small">Source</div><div class="fw-semibold">{{ $coupure->source ?: 'Non renseignée' }}</div></div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Signalements</h3></div>
                <div class="card-body"><div class="h1 mb-1">{{ $coupure->signalements_count }}</div><div class="text-secondary">signalement{{ $coupure->signalements_count > 1 ? 's' : '' }} associé{{ $coupure->signalements_count > 1 ? 's' : '' }}</div><a class="btn btn-outline-primary btn-sm mt-3" href="{{ route('signalements.index') }}">Voir mes signalements</a></div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Planning</h3></div>
                <div class="card-body small"><div class="text-secondary">Début</div><div class="fw-semibold mb-3">{{ $coupure->date_debut?->format('d/m/Y H:i') ?? 'À confirmer' }}</div><div class="text-secondary">Fin estimée</div><div class="fw-semibold">{{ $coupure->date_fin_estimee?->format('d/m/Y H:i') ?? 'À confirmer' }}</div></div>
            </div>
        </div>
    </div>
@endsection
