@extends('layouts.user')

@section('title', 'Points de fraîcheur')

@section('content')
    <div class="page-header">
        <div class="page-pretitle">Points de fraîcheur</div>
        <h2 class="page-title">Restez au frais près de chez vous</h2>
        <div class="text-secondary">Trouvez des lieux utiles près de votre quartier pendant les fortes chaleurs.</div>
    </div>

    @if (! $quartier)
        <div class="alert alert-warning">
            Veuillez sélectionner votre quartier dans votre profil pour voir les points de fraîcheur disponibles.
            <a class="alert-link" href="{{ route('profile.edit') }}">Choisir mon quartier</a>
        </div>
    @else
        <div class="row row-cards mb-4">
            <div class="col-sm-6 col-lg-4"><div class="card card-sm h-100"><div class="card-body"><div class="text-secondary">Quartier actuel</div><div class="h2 mb-0">{{ $quartier->nom }}</div><div class="small text-secondary">{{ $quartier->ville }}</div></div></div></div>
            <div class="col-sm-6 col-lg-4"><div class="card card-sm h-100"><div class="card-body"><div class="text-secondary">Points disponibles</div><div class="h1 mb-0">{{ $points->count() }}</div><div class="small text-secondary">Lieux validés HeatAlert</div></div></div></div>
            <div class="col-sm-6 col-lg-4"><div class="card card-sm h-100"><div class="card-body"><div class="text-secondary">Meilleure note moyenne</div><div class="h1 mb-0">{{ $meilleureNote ? number_format($meilleureNote, 1, ',', ' ') . ' / 5' : '—' }}</div><div class="small text-secondary">Basée sur les avis publiés</div></div></div></div>
        </div>

        @if ($points->isNotEmpty())
            <div class="card mb-4"><div class="card-header"><h3 class="card-title">Carte des points disponibles <span class="badge bg-blue-lt text-blue ms-1">OpenStreetMap</span></h3></div><div class="card-body"><div id="points-fraicheur-map" class="rounded border" style="height: 360px;"></div></div></div>
        @endif

        <div class="row row-cards">
            @forelse ($points as $point)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex gap-3"><span class="avatar bg-cyan-lt text-cyan">@include('partials.tabler-icon', ['name' => 'map-pin'])</span><div class="flex-fill"><h3 class="card-title mb-1">{{ $point->nom }}</h3><div class="text-secondary small">{{ $point->type }}</div></div></div>
                            <div class="mt-3 small text-secondary">{{ $point->adresse }}</div>
                            @if ($point->horaires)<div class="mt-2 small"><span class="text-secondary">Horaires :</span> {{ $point->horaires }}</div>@endif
                            <div class="mt-3 d-flex align-items-center justify-content-between"><span class="badge bg-yellow-lt text-yellow">{{ $point->note_moyenne ? number_format($point->note_moyenne, 1, ',', ' ') . ' / 5' : 'Pas encore noté' }}</span><span class="small text-secondary">{{ $point->avis_publies_count }} avis</span></div>
                            <div class="mt-3 d-flex flex-wrap gap-1">@if($point->climatise)<span class="badge bg-blue-lt text-blue">Climatisé</span>@endif @if($point->eau_disponible)<span class="badge bg-cyan-lt text-cyan">Eau disponible</span>@endif @if($point->accessible_pmr)<span class="badge bg-green-lt text-green">Accessible PMR</span>@endif</div>
                        </div>
                        <div class="card-footer bg-transparent"><a class="btn btn-outline-primary btn-sm" href="{{ route('points-fraicheur.show', $point) }}">Voir le détail</a></div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="empty border rounded bg-white py-5"><div class="empty-img"><span class="avatar avatar-xl bg-cyan-lt text-cyan">@include('partials.tabler-icon', ['name' => 'map-pin', 'class' => 'icon icon-3'])</span></div><p class="empty-title">Aucun point de fraîcheur disponible actuellement dans votre quartier.</p><p class="empty-subtitle text-secondary">Revenez bientôt : l’administration valide progressivement de nouveaux lieux.</p></div></div>
            @endforelse
        </div>

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
@endsection
