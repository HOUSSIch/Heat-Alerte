@extends('layouts.front')

@section('title', $etablissementSensible->nom)

@section('content')
    @php
        $structureIcon = str_contains(strtolower($etablissementSensible->type), 'sant')
            ? 'bi-heart-pulse'
            : (str_contains(strtolower($etablissementSensible->type), 'crèche')
                ? 'bi-balloon'
                : (str_contains(strtolower($etablissementSensible->type), 'école') ? 'bi-backpack' : 'bi-people'));
    @endphp

    @include('partials.front-page-title', [
        'title' => 'Structure d’assistance',
        'subtitle' => $etablissementSensible->type . ' • ' . ($etablissementSensible->quartier?->nom ?? 'Quartier'),
    ])

    <section id="service-details" class="service-details section">
        <div class="container" data-aos="fade-up">
            <div class="row g-5">
                <div class="col-lg-4">
                    <aside class="sidebar">
                        <div class="services-list">
                            <h4>Type de structure</h4>
                            <ul>
                                <li class="active"><a href="#">{{ $etablissementSensible->type }} <i class="bi bi-building"></i></a></li>
                                <li><a href="#">Structure active</a></li>
                            </ul>
                        </div>
                        <div class="help-card">
                            <i class="bi bi-people"></i>
                            <h4>{{ $etablissementSensible->accueil_public ? 'Accueil du public' : 'Information uniquement' }}</h4>
                            <p>{{ $etablissementSensible->accueil_public ? 'Vous pouvez contacter ou vous rendre à cette structure si nécessaire.' : 'Cet établissement n’est pas un lieu d’accueil public.' }}</p>
                            <a href="{{ route('etablissements-sensibles.index') }}" class="btn-main">Retour aux structures</a>
                        </div>
                    </aside>
                </div>

                <div class="col-lg-8">
                    <article class="detail heatalert-detail-content">
                        <div class="heatalert-detail-heading--compact">
                            <span class="heatalert-icon"><i class="bi {{ $structureIcon }}"></i></span>
                            <div>
                                <p>{{ $etablissementSensible->type }}</p>
                                <h2>{{ $etablissementSensible->nom }}</h2>
                            </div>
                        </div>
                        <p class="lead">{{ $etablissementSensible->description ?: 'Une structure de proximité recensée par HeatAlert.' }}</p>

                        <h3>Informations pratiques</h3>
                        <div class="row g-3 includes">
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Quartier : {{ $etablissementSensible->quartier?->nom }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Adresse : {{ $etablissementSensible->adresse }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Téléphone : {{ $etablissementSensible->telephone ?: 'Non renseigné' }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Horaires : {{ $etablissementSensible->horaires ?: 'Non renseignés' }}</div></div>
                        </div>

                        <h3>Services proposés</h3>
                        <p>{{ $etablissementSensible->services ?: 'Non renseignés' }}</p>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
