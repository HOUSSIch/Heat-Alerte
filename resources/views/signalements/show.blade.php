@extends('layouts.front')

@section('title', 'Détail signalement')

@section('content')
    @include('partials.front-page-title', ['title' => 'Détail du signalement', 'subtitle' => $signalement->titre])

    <section id="service-details" class="service-details section">
        <div class="container" data-aos="fade-up">
            <div class="row g-5">
                <div class="col-lg-4">
                    <aside class="sidebar">
                        <div class="services-list">
                            <h4>Statut</h4>
                            <ul>
                                <li class="active"><a href="#">{{ str_replace('_', ' ', ucfirst($signalement->statut)) }} <i class="bi bi-exclamation-triangle"></i></a></li>
                                <li><a href="#">Coupure : {{ $signalement->coupureElectrique?->titre ?? 'Aucune associée' }}</a></li>
                            </ul>
                        </div>
                        <div class="help-card">
                            <i class="bi bi-arrow-left"></i>
                            <h4>Actions</h4>
                            <p>Retrouvez l’ensemble de vos signalements.</p>
                            <a href="{{ route('signalements.index') }}" class="btn-main">Retour à mes signalements</a>
                        </div>
                    </aside>
                </div>

                <div class="col-lg-8">
                    <article class="detail heatalert-detail-content">
                        @if ($signalement->latitude && $signalement->longitude)
                            <div id="signalement-map" class="heatalert-detail-map"></div>
                        @endif

                        <div class="heatalert-detail-heading">
                            <h2>{{ $signalement->titre }}</h2>
                            <p class="lead">{{ $signalement->description }}</p>
                        </div>

                        <h3>Informations</h3>
                        <div class="row g-3 includes">
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Quartier : {{ $signalement->quartier?->nom }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Adresse : {{ $signalement->adresse ?: 'Non renseignée' }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Date : {{ $signalement->date_signalement->format('d/m/Y H:i') }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Coupure associée : {{ $signalement->coupureElectrique?->titre ?? 'Aucune' }}</div></div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    @if ($signalement->latitude && $signalement->longitude)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha384-sHL9NAb7lN7rfvG5lfHpm643Xkcjzp4jFvuavGOndn6pjVqS6ny56CAt3nsEVT4H" crossorigin="anonymous">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH" crossorigin="anonymous"></script>
        <script>
            const signalementMap = L.map('signalement-map').setView([{{ $signalement->latitude }}, {{ $signalement->longitude }}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
            }).addTo(signalementMap);
            L.marker([{{ $signalement->latitude }}, {{ $signalement->longitude }}]).addTo(signalementMap);
        </script>
    @endif
@endsection
