@extends('layouts.front')

@section('title', 'Météo et alertes - HeatAlert')

@section('content')
    @include('partials.front-page-title', [
        'title' => 'Météo & alertes',
        'subtitle' => 'Conditions actuelles et alertes de votre quartier.',
    ])

    <section class="services section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Situation actuelle</h2>
            <p>Les informations utiles pour anticiper les fortes chaleurs.</p>
        </div>
        <div class="container">
            @if (!$quartier)
                <div class="heatalert-notice alert alert-warning">Veuillez sélectionner votre quartier dans votre profil.</div>
            @elseif($errors->has('meteo'))
                <div class="heatalert-notice alert alert-danger">{{ $errors->first('meteo') }}</div>
            @else
                <div class="row gy-4">
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item d-flex position-relative h-100">
                            <i class="bi bi-sun icon flex-shrink-0"></i>
                            <div><h4>Température actuelle</h4><p class="heatalert-stat">{{ $weather['temperature'] }}°C</p><p>{{ $quartier->nom }} · {{ $quartier->ville }}</p></div>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item d-flex position-relative h-100">
                            <i class="bi bi-thermometer-half icon flex-shrink-0"></i>
                            <div><h4>Prévisions du jour</h4><p><strong>Minimum :</strong> {{ $weather['temperature_min'] }}°C</p><p><strong>Maximum :</strong> {{ $weather['temperature_max'] }}°C</p></div>
                        </div>
                    </div>
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item d-flex position-relative h-100">
                            <i class="bi bi-shield-exclamation icon flex-shrink-0"></i>
                            <div><h4>Niveau d’alerte</h4><p class="mb-2"><span class="badge text-bg-{{ ['Normal' => 'success', 'Vigilance' => 'warning', 'Alerte' => 'warning', 'Critique' => 'danger', 'Danger' => 'danger'][$niveau] }}">{{ $niveau }}</span></p><p>Restez attentif aux bulletins publiés ci-dessous.</p></div>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3"><div><h3>Vigilance météo sur 7 jours</h3><p class="mb-0">Seuils de vigilance internes HeatAlert.</p></div></div>
                    <div class="row gy-3">
                        @foreach ($weather['days'] as $day)
                            @php($dayNiveau = $day['niveau'] ?? app(\App\Services\WeatherRiskService::class)->level($day['temperature_max']))
                            <div class="col-lg-3 col-md-4"><div class="heatalert-card h-100"><p class="post-category">{{ \Carbon\Carbon::parse($day['date'])->locale('fr')->translatedFormat('l d M') }}</p><h3>{{ $day['temperature_max'] }}°C</h3><p>Min. {{ $day['temperature_min'] }}°C</p><span class="badge text-bg-{{ ['Normal' => 'success', 'Vigilance' => 'warning', 'Alerte' => 'warning', 'Critique' => 'danger'][$dayNiveau] }}">{{ $dayNiveau }}</span></div></div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="blog-posts section pt-0">
        <div class="container section-title" data-aos="fade-up"><h2>Bulletins publiés</h2><p>Alertes météo actives pour votre zone.</p></div>
        <div class="container"><div class="row gy-4">
            @forelse($alertesActives as $alerte)
                <div class="col-lg-6" data-aos="fade-up"><article class="heatalert-card"><div class="d-flex justify-content-between gap-3 align-items-start"><div><p class="post-category">Alerte HeatAlert</p><h2 class="title">{{ $alerte->titre }}</h2></div><i class="bi bi-exclamation-triangle heatalert-icon"></i></div><span class="badge text-bg-{{ ['Normal' => 'success', 'Vigilance' => 'warning', 'Alerte' => 'warning', 'Critique' => 'danger', 'Danger' => 'danger'][$alerte->niveau] }}">{{ $alerte->niveau }}</span><p class="mt-3">{{ $alerte->description }}</p>@if ($alerte->temperature)<p><strong>Température annoncée :</strong> {{ $alerte->temperature }}°C</p>@endif<div class="post-meta">{{ $alerte->source === 'manuel' ? 'Publié par l’administration' : 'Généré depuis Open-Meteo' }} · {{ $alerte->date_debut?->format('d/m/Y H:i') }} @if ($alerte->date_fin) → {{ $alerte->date_fin->format('d/m/Y H:i') }} @endif</div></article></div>
            @empty
                <div class="col-12"><div class="heatalert-panel text-center" data-aos="fade-up"><i class="bi bi-check-circle heatalert-icon mb-3"></i><h3>Aucune alerte active</h3><p class="mb-0">Votre quartier ne présente aucun bulletin météo actif.</p></div></div>
            @endforelse
        </div></div>
    </section>
@endsection
