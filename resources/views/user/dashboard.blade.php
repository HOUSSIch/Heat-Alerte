@extends('layouts.user')

@section('title', 'Dashboard - HeatAlert')

@section('content')
    @php
        $temperatureLabel = $weather ? $weather['temperature'].'°C' : '—';
        $niveauLive = $niveau ?? 'Indisponible';
        $niveauColor = [
            'Normal' => 'green',
            'Vigilance' => 'yellow',
            'Alerte' => 'orange',
            'Danger' => 'red',
        ][$niveauLive] ?? 'secondary';
    @endphp

    <div class="page-header">
        <div class="page-pretitle">Espace habitant</div>
        <h2 class="page-title">Bonjour, {{ auth()->user()->name }}</h2>
        <div class="text-secondary">Situation HeatAlert de {{ $quartier?->nom ?? 'votre quartier' }}.</div>
    </div>

    <div class="row row-cards">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-orange-lt text-orange">@include('partials.tabler-icon', ['name' => 'thermometer'])</span></div>
                        <div class="col">
                            <div class="text-secondary">Température actuelle</div>
                            <div class="h1 mb-0">{{ $temperatureLabel }}</div>
                            <div class="small text-secondary">Open-Meteo</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-{{ $niveauColor }}-lt text-{{ $niveauColor }}">@include('partials.tabler-icon', ['name' => 'alert'])</span></div>
                        <div class="col">
                            <div class="text-secondary">Alerte canicule</div>
                            <div class="h2 mb-0">{{ $niveauLive }}</div>
                            <div class="small text-secondary">{{ $weather ? 'Alerte canicule live' : 'Aucune donnée live' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-yellow-lt text-yellow">@include('partials.tabler-icon', ['name' => 'bolt'])</span></div>
                        <div class="col">
                            <div class="text-secondary">Coupures actives</div>
                            <div class="h1 mb-0">{{ $coupuresActives }}</div>
                            <div class="small text-secondary">{{ $signalementsEnAttente }} signalement{{ $signalementsEnAttente > 1 ? 's' : '' }} en attente</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto"><span class="avatar bg-blue-lt text-blue">@include('partials.tabler-icon', ['name' => 'devices'])</span></div>
                        <div class="col">
                            <div class="text-secondary">Mes équipements</div>
                            <div class="h1 mb-0">{{ auth()->user()->equipements()->count() }}</div>
                            <a class="small" href="{{ route('equipements.index') }}">Voir mes équipements</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards mt-1">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <div>
                        <div class="card-title mb-0">Météo en direct <span class="badge bg-blue-lt text-blue ms-1">LIVE · Open-Meteo</span></div>
                    </div>
                    <div class="card-actions"><a class="btn btn-sm btn-outline-primary" href="{{ route('meteo.index') }}">Détail météo</a></div>
                </div>
                <div class="card-body">
                    @if ($weather && $quartier)
                        <div class="row row-cards">
                            <div class="col-sm-6 col-xl-3"><div class="text-secondary">Quartier</div><div class="fw-bold">{{ $quartier->nom }}</div><div class="small text-secondary">{{ $quartier->ville }}</div></div>
                            <div class="col-sm-6 col-xl-3"><div class="text-secondary">Température actuelle</div><div class="h2 mb-0">{{ $weather['temperature'] }}°C</div></div>
                            <div class="col-sm-6 col-xl-3"><div class="text-secondary">Minimum / Maximum</div><div class="fw-bold">{{ $weather['temperature_min'] }}° / {{ $weather['temperature_max'] }}°</div></div>
                            <div class="col-sm-6 col-xl-3"><div class="text-secondary">Niveau live</div><span class="badge bg-{{ $niveauColor }}-lt text-{{ $niveauColor }}">{{ $niveauLive }}</span></div>
                        </div>
                        <div class="small text-secondary mt-3">Source : Open-Meteo · données météo en temps réel.</div>
                    @else
                        <div class="alert alert-info mb-0">Météo momentanément indisponible. Consultez le détail pour réessayer.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h3 class="card-title">Conseils du jour</h3></div>
                <div class="list-group list-group-flush">
                    @forelse ($conseils as $conseil)
                        <div class="list-group-item">
                            <span class="badge bg-blue-lt text-blue">{{ $conseil->niveau }}</span>
                            <span class="ms-1">{{ $conseil->titre }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-secondary">Aucun conseil personnalisé pour le moment.</div>
                    @endforelse
                </div>
                <div class="card-footer"><a class="btn btn-outline-primary w-100" href="{{ route('conseils.mes') }}">Voir tous les conseils</a></div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <div>
                <div class="card-title mb-0">Bulletins et alertes publiés</div>
                <div class="text-secondary small">Informations publiées pour votre quartier.</div>
            </div>
        </div>
        <div class="card-body">
            <div class="row row-cards">
                @forelse ($alertesActives as $alerte)
                    <div class="col-md-6 col-xl-4">
                        <div class="alert alert-warning h-100 mb-0">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <strong>{{ $alerte->titre }}</strong>
                                <span class="badge bg-orange">{{ $alerte->niveau }}</span>
                            </div>
                            @if (! is_null($alerte->temperature))
                                <div class="mt-2">Température annoncée : {{ $alerte->temperature }}°C</div>
                            @endif
                            @if (! is_null($alerte->temperature_min) || ! is_null($alerte->temperature_max))
                                <div>Minimum / Maximum : {{ $alerte->temperature_min ?? '—' }}° / {{ $alerte->temperature_max ?? '—' }}°</div>
                            @endif
                            @if ($alerte->description)
                                <div class="mt-2">{{ $alerte->description }}</div>
                            @endif
                            <div class="small text-secondary mt-3">
                                Début : {{ $alerte->date_debut?->format('d/m/Y H:i') ?? 'non précisé' }}<br>
                                Fin : {{ $alerte->date_fin?->format('d/m/Y H:i') ?? 'non précisée' }}<br>
                                Source : {{ $alerte->source === 'manuel' ? 'Administration HeatAlert' : 'Open-Meteo' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><div class="alert alert-success mb-0">Aucune alerte ou bulletin actif pour votre quartier.</div></div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row row-cards mt-1">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header"><h3 class="card-title">Mes équipements sensibles</h3></div>
                <div class="card-body">
                    <div class="row row-cards">
                        @forelse ($equipements as $equipement)
                            <div class="col-md-4">
                                <div class="card card-sm h-100">
                                    <div class="card-body">
                                        <div class="fw-bold">{{ $equipement->nom }}</div>
                                        <div class="text-secondary small">{{ $equipement->type }}</div>
                                        <div class="mt-2">
                                            @if ($equipement->sensible_chaleur)<span class="badge bg-orange-lt text-orange">Chaleur</span>@endif
                                            @if ($equipement->sensible_coupure)<span class="badge bg-red-lt text-red">Coupure</span>@endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><div class="alert alert-info mb-0">Aucun équipement enregistré.</div></div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer"><a class="btn btn-outline-primary" href="{{ route('equipements.index') }}">Voir mes équipements</a></div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 bg-orange-lt">
                <div class="card-body d-flex flex-column">
                    <span class="avatar bg-orange text-white mb-3">@include('partials.tabler-icon', ['name' => 'message'])</span>
                    <h3 class="card-title">Assistant HeatAlert</h3>
                    <p class="text-secondary">Besoin d'aide pour vos équipements ou les fortes chaleurs ?</p>
                    <div class="mt-auto"><a class="btn btn-orange w-100" href="{{ route('chatbot.index') }}">Ouvrir l'assistant</a></div>
                </div>
            </div>
        </div>
    </div>
@endsection
