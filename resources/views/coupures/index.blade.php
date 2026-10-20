@extends('layouts.front')

@section('title', 'Coupures électriques')

@section('content')
    @include('partials.front-page-title', ['title' => 'Coupures électriques', 'subtitle' => 'Les interventions et interruptions signalées dans votre quartier.'])
    <section class="services section">
        <div class="container outage-page-header mb-5" data-aos="fade-up"><div class="section-title text-start p-0 m-0"><h2>Suivi du réseau</h2><p>{{ $coupuresEnCours }} coupure(s) en cours · {{ $coupuresPrevues }} prévue(s)</p></div><div class="outage-page-actions"><a class="outage-page-action outage-page-action-secondary" href="{{ route('coupures.planning') }}"><i class="bi bi-calendar3"></i> Planning des coupures</a><a class="outage-page-action outage-page-action-primary" href="{{ route('signalements.create') }}"><i class="bi bi-lightning-charge"></i> Signaler une coupure</a></div></div>
        <div class="container">
            @if ($sansQuartier)
                <div class="heatalert-notice alert alert-warning">Votre profil n’est associé à aucun quartier. Complétez votre profil pour consulter les coupures locales.</div>
            @endif
            <div class="row gy-4">
            @forelse ($coupures as $coupure)
                <div class="col-lg-4 col-md-6" data-aos="fade-up"><div class="service-item d-flex position-relative h-100"><i class="bi bi-lightning-charge icon flex-shrink-0"></i><div><h4>{{ $coupure->titre }}</h4><p><span class="badge text-bg-{{ $coupure->statut === 'en_cours' ? 'danger' : ($coupure->statut === 'prévue' ? 'primary' : 'secondary') }}">{{ str_replace('_', ' ', ucfirst($coupure->statut)) }}</span></p><p>{{ $coupure->quartier?->nom ?? 'Quartier non renseigné' }} · {{ $coupure->type ?: 'Type non renseigné' }}</p><p><i class="bi bi-clock"></i> {{ $coupure->date_debut?->format('d/m/Y H:i') ?? 'Début à confirmer' }}</p><p>{{ $coupure->description ?: $coupure->cause ?: 'Information en cours de mise à jour.' }}</p><a href="{{ route('coupures.show', $coupure) }}" class="readmore stretched-link"><span>Voir le détail</span><i class="bi bi-arrow-right"></i></a></div></div></div>
            @empty
                <div class="col-12"><div class="heatalert-panel text-center"><i class="bi bi-lightning-charge heatalert-icon mb-3"></i><h3>Aucune coupure active</h3><p>Vous pouvez signaler une interruption pour aider votre quartier.</p><a class="btn-get-started" href="{{ route('signalements.create') }}">Signaler une coupure</a></div></div>
            @endforelse
        </div></div>
    </section>
@endsection
