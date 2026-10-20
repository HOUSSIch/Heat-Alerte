@extends('layouts.front')

@section('title', 'Points de fraîcheur')

@section('content')
    @include('partials.front-page-title', ['title' => 'Points de fraîcheur', 'subtitle' => 'Des lieux utiles pour vous rafraîchir près de chez vous.'])
    <section class="portfolio section">
        <div class="container section-title" data-aos="fade-up"><h2>Se rafraîchir en sécurité</h2><p>Des lieux validés HeatAlert dans votre quartier.</p></div>
        <div class="container">
            @if (!$quartier)
                <div class="heatalert-notice alert alert-warning">Veuillez sélectionner votre quartier dans votre profil. <a href="{{ route('profile.edit') }}">Choisir mon quartier</a></div>
            @else
                @if ($points->isNotEmpty())
                    <div class="heatalert-panel mb-5" data-aos="fade-up"><div class="d-flex justify-content-between align-items-center mb-3"><h3 class="mb-0">Carte des points disponibles</h3><span class="badge text-bg-primary">OpenStreetMap</span></div><div id="points-fraicheur-map" style="height: 360px;"></div></div>
                @endif
                <form method="GET" action="{{ route('points-fraicheur.compare') }}">
                    <div class="points-compare-toolbar mb-4">
                        <div>
                            <span class="points-compare-kicker"><i class="bi bi-arrow-left-right"></i> Comparaison</span>
                            <p class="mb-0">Sélectionnez 2 à 3 points pour les comparer.</p>
                        </div>
                        <button class="compare-submit" type="submit">
                            <i class="bi bi-bar-chart-line"></i>
                            Comparer la sélection
                        </button>
                    </div>
                    <div class="row gy-4 isotope-container" data-aos="fade-up">
                    @forelse ($points as $point)
                        <div class="col-lg-4 col-md-6 portfolio-item"><div class="heatalert-card"><label class="point-compare-toggle"><input class="point-compare-input" type="checkbox" name="points[]" value="{{ $point->id }}"><span class="point-compare-indicator"><i class="bi bi-check-lg"></i></span><span>Ajouter à la comparaison</span></label><div class="d-flex justify-content-between align-items-start"><i class="bi bi-geo-alt heatalert-icon"></i><span class="badge text-bg-warning">{{ $point->note_moyenne ? number_format($point->note_moyenne, 1, ',', ' ') . ' / 5' : 'Pas encore noté' }}</span></div><h3 class="mt-4">{{ $point->nom }}</h3><p>{{ $point->type }} · {{ $point->adresse }}</p>@if ($point->horaires)<p><i class="bi bi-clock"></i> {{ $point->horaires }}</p>@endif<div class="d-flex flex-wrap gap-2 mb-4">@if ($point->climatise)<span class="badge text-bg-primary">Climatisé</span>@endif @if ($point->eau_disponible)<span class="badge text-bg-info">Eau disponible</span>@endif @if ($point->accessible_pmr)<span class="badge text-bg-success">Accessible PMR</span>@endif</div><a href="{{ route('points-fraicheur.show', $point) }}" class="readmore"><span>Voir le détail</span><i class="bi bi-arrow-right"></i></a></div></div>
                    @empty
                        <div class="col-12"><div class="heatalert-panel text-center"><i class="bi bi-geo-alt heatalert-icon mb-3"></i><h3>Aucun point disponible</h3><p class="mb-0">Revenez bientôt : de nouveaux lieux sont validés progressivement.</p></div></div>
                    @endforelse
                    </div>
                </form>
                @if ($points->isNotEmpty())
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                    <script>
                        const freshPoints = @json($mapPoints);
                        const pointsMap = L.map('points-fraicheur-map').setView([{{ $quartier->latitude }}, {{ $quartier->longitude }}], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(pointsMap);
                        freshPoints.forEach((point) => {
                            const content = document.createElement('div');
                            const name = document.createElement('strong'); name.textContent = point.nom;
                            const type = document.createElement('div'); type.textContent = point.type;
                            const address = document.createElement('small'); address.textContent = point.adresse;
                            const link = document.createElement('a'); link.href = point.url; link.textContent = 'Voir le détail'; link.className = 'd-block mt-2';
                            content.append(name, type, address, link);
                            L.marker([point.latitude, point.longitude]).addTo(pointsMap).bindPopup(content);
                        });
                    </script>
                @endif
            @endif
        </div>
    </section>
@endsection
