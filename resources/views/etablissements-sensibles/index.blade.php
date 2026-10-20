@extends('layouts.front')

@section('title', 'Structures d’assistance')

@section('content')
    @include('partials.front-page-title', ['title' => 'Structures d’assistance', 'subtitle' => 'Des contacts et services de proximité utiles en période de chaleur.'])

    <section class="services section">
        <div class="container section-title" data-aos="fade-up"><h2>Près de chez vous</h2><p>Des informations fiables pour votre quartier.</p></div>
        <div class="container">
            @if (!$quartier)
                <div class="heatalert-notice alert alert-warning">Veuillez sélectionner votre quartier dans votre profil. <a href="{{ route('profile.edit') }}">Choisir mon quartier</a></div>
            @else
                <div class="row gy-4">
                    @forelse ($etablissements as $etablissementSensible)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up">
                            <div class="service-item d-flex position-relative h-100">
                                <i class="bi bi-building icon flex-shrink-0"></i>
                                <div><h4>{{ $etablissementSensible->nom }}</h4><p><strong>{{ $etablissementSensible->type }}</strong> · {{ $etablissementSensible->quartier?->nom }}</p><p>{{ $etablissementSensible->adresse }}</p>@if ($etablissementSensible->telephone)<p><i class="bi bi-telephone"></i> {{ $etablissementSensible->telephone }}</p>@endif<p><i class="bi bi-clock"></i> {{ $etablissementSensible->horaires ?: 'Horaires non renseignés' }}</p><span class="badge text-bg-{{ $etablissementSensible->accueil_public ? 'success' : 'secondary' }}">{{ $etablissementSensible->accueil_public ? 'Accueil du public : Oui' : 'Information uniquement' }}</span><a class="readmore stretched-link" href="{{ route('etablissements-sensibles.show', $etablissementSensible) }}"><span>Voir la structure</span><i class="bi bi-arrow-right"></i></a></div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><div class="heatalert-panel text-center"><i class="bi bi-building heatalert-icon mb-3"></i><h3>Aucune structure disponible</h3><p class="mb-0">Les informations seront ajoutées progressivement par l’administration.</p></div></div>
                    @endforelse
                </div>
            @endif
        </div>
    </section>
@endsection
