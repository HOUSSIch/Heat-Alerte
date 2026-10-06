@extends('layouts.user')

@section('title', 'Coupures électriques')

@section('content')
    @php
        $statutClasses = [
            'prévue' => 'bg-blue-lt text-blue',
            'en_cours' => 'bg-orange-lt text-orange',
            'terminée' => 'bg-green-lt text-green',
            'annulée' => 'bg-secondary-lt text-secondary',
        ];
    @endphp

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Réseau électrique</div>
                <h2 class="page-title">Coupures électriques</h2>
                <div class="text-secondary">Consultez les coupures prévues ou en cours dans votre quartier.</div>
            </div>
            <div class="col-auto mt-3 mt-sm-0">
                <a class="btn btn-primary" href="{{ route('signalements.create') }}">
                    @include('partials.tabler-icon', ['name' => 'alert', 'class' => 'icon icon-2 me-1'])
                    Signaler une coupure
                </a>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm h-100">
                <div class="card-body"><div class="row align-items-center">
                    <div class="col-auto"><span class="avatar bg-orange-lt text-orange">@include('partials.tabler-icon', ['name' => 'bolt'])</span></div>
                    <div class="col"><div class="text-secondary">Coupures en cours</div><div class="h1 mb-0">{{ $coupuresEnCours }}</div><div class="small text-secondary">Interventions actuellement signalées</div></div>
                </div></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm h-100">
                <div class="card-body"><div class="row align-items-center">
                    <div class="col-auto"><span class="avatar bg-blue-lt text-blue">@include('partials.tabler-icon', ['name' => 'alert'])</span></div>
                    <div class="col"><div class="text-secondary">Coupures prévues</div><div class="h1 mb-0">{{ $coupuresPrevues }}</div><div class="small text-secondary">Maintenances annoncées</div></div>
                </div></div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm h-100">
                <div class="card-body"><div class="row align-items-center">
                    <div class="col-auto"><span class="avatar bg-yellow-lt text-yellow">@include('partials.tabler-icon', ['name' => 'alert'])</span></div>
                    <div class="col"><div class="text-secondary">Mes signalements en attente</div><div class="h1 mb-0">{{ $signalementsEnAttente }}</div><a class="small" href="{{ route('signalements.index') }}">Voir mes signalements</a></div>
                </div></div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">Coupures dans votre quartier</h3>
        <span class="badge bg-blue-lt text-blue">{{ $coupures->count() }} résultat{{ $coupures->count() > 1 ? 's' : '' }}</span>
    </div>

    <div class="row row-cards">
        @forelse ($coupures as $coupure)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <span class="avatar bg-orange-lt text-orange flex-shrink-0">@include('partials.tabler-icon', ['name' => 'bolt'])</span>
                            <div class="flex-fill">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <h3 class="card-title mb-1">{{ $coupure->titre }}</h3>
                                    <span class="badge {{ $statutClasses[$coupure->statut] ?? 'bg-secondary-lt text-secondary' }}">{{ str_replace('_', ' ', ucfirst($coupure->statut)) }}</span>
                                </div>
                                <div class="text-secondary small">{{ $coupure->quartier?->nom ?? 'Quartier non renseigné' }} · {{ $coupure->type ?: 'Type non renseigné' }}</div>
                            </div>
                        </div>

                        <div class="mt-4 small">
                            <div class="mb-2"><span class="text-secondary">Cause</span><br><span>{{ $coupure->cause ?: 'Non renseignée' }}</span></div>
                            <div class="mb-2"><span class="text-secondary">Début</span><br><span>{{ $coupure->date_debut?->format('d/m/Y H:i') ?? 'À confirmer' }}</span></div>
                            <div class="mb-2"><span class="text-secondary">Fin estimée</span><br><span>{{ $coupure->date_fin_estimee?->format('d/m/Y H:i') ?? 'À confirmer' }}</span></div>
                            @if ($coupure->adresse)
                                <div><span class="text-secondary">Adresse</span><br><span>{{ $coupure->adresse }}</span></div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('coupures.show', $coupure) }}" class="btn btn-outline-primary btn-sm">Voir les détails</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty border rounded bg-white py-5">
                    <div class="empty-img"><span class="avatar avatar-xl bg-green-lt text-green">@include('partials.tabler-icon', ['name' => 'bolt', 'class' => 'icon icon-3'])</span></div>
                    <p class="empty-title">Aucune coupure active dans votre quartier.</p>
                    <p class="empty-subtitle text-secondary">Vous pouvez signaler une interruption pour aider à informer les autres habitants.</p>
                    <a class="btn btn-primary" href="{{ route('signalements.create') }}">Signaler une coupure</a>
                </div>
            </div>
        @endforelse
    </div>
@endsection
