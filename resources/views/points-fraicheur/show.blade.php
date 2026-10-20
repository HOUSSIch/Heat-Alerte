@extends('layouts.front')

@section('title', $pointFraicheur->nom)

@section('content')
    @include('partials.front-page-title', [
        'title' => 'Point de fraîcheur',
        'subtitle' => $pointFraicheur->type . ' • ' . ($pointFraicheur->quartier?->nom ?? 'Quartier'),
    ])

    <section id="portfolio-details" class="portfolio-details section">
        <div class="container" data-aos="fade-up">
            <div class="row gy-5">
                <div class="col-lg-8">
                    <div id="point-fraicheur-map" class="heatalert-detail-map heatalert-detail-map--large"></div>
                </div>
                <div class="col-lg-4">
                    <aside class="meta-card">
                        <h3>Informations pratiques</h3>
                        <ul>
                            <li><span class="lbl">Type</span><span class="val">{{ $pointFraicheur->type }}</span></li>
                            <li><span class="lbl">Quartier</span><span class="val">{{ $pointFraicheur->quartier?->nom }}</span></li>
                            <li><span class="lbl">Adresse</span><span class="val">{{ $pointFraicheur->adresse }}</span></li>
                            <li><span class="lbl">Horaires</span><span class="val">{{ $pointFraicheur->horaires ?: 'Non renseignés' }}</span></li>
                            <li><span class="lbl">Téléphone</span><span class="val">{{ $pointFraicheur->telephone ?: 'Non renseigné' }}</span></li>
                        </ul>
                        <a href="{{ route('points-fraicheur.index') }}" class="btn-main"><i class="bi bi-arrow-left"></i> Retour à la liste</a>
                    </aside>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <article class="writeup heatalert-detail-content" data-aos="fade-up">
                        <div class="heatalert-detail-heading">
                            <h2>{{ $pointFraicheur->nom }}</h2>
                            <p class="lead">{{ $pointFraicheur->description ?: 'Un lieu identifié pour vous aider à rester au frais pendant les fortes chaleurs.' }}</p>
                        </div>
                        <div class="row g-3 highlights">
                            <div class="col-md-4"><div class="hl"><i class="bi bi-snow"></i><span>{{ $pointFraicheur->climatise ? 'Climatisé' : 'Sans climatisation' }}</span></div></div>
                            <div class="col-md-4"><div class="hl"><i class="bi bi-droplet"></i><span>{{ $pointFraicheur->eau_disponible ? 'Eau disponible' : 'Eau non renseignée' }}</span></div></div>
                            <div class="col-md-4"><div class="hl"><i class="bi bi-universal-access"></i><span>{{ $pointFraicheur->accessible_pmr ? 'Accessible PMR' : 'Accessibilité non renseignée' }}</span></div></div>
                        </div>
                    </article>
                </div>
            </div>

            <div class="row gy-5 mt-1">
                <div class="col-lg-8">
                    <article class="detail">
                        <h3>Avis des habitants</h3>
                        @forelse ($avisPublies as $avis)
                            <div class="inc mb-3">
                                <i class="bi bi-star-fill"></i>
                                <div><strong>{{ $avis->user?->name ?? 'Habitant' }} — {{ $avis->note }}/5</strong><br>{{ $avis->commentaire }}<br><small>{{ $avis->created_at->format('d/m/Y') }}</small></div>
                            </div>
                        @empty
                            <p>Aucun avis publié pour le moment.</p>
                        @endforelse
                    </article>
                </div>
                <div class="col-lg-4">
                    <aside class="sidebar">
                        <div class="services-list">
                            <h4>Évaluation</h4>
                            <ul>
                                <li class="active"><a href="#">{{ $pointFraicheur->note_moyenne ? number_format($pointFraicheur->note_moyenne, 1, ',', ' ') . ' / 5' : '—' }} <i class="bi bi-star"></i></a></li>
                                <li><a href="#">{{ $pointFraicheur->avis_publies_count }} avis publié{{ $pointFraicheur->avis_publies_count > 1 ? 's' : '' }}</a></li>
                            </ul>
                        </div>
                        <div class="help-card">
                            <i class="bi bi-chat-dots"></i>
                            <h4>{{ $monAvis ? 'Modifier mon avis' : 'Mon avis' }}</h4>
                            <form method="POST" action="{{ $monAvis ? route('points-fraicheur.avis.update', [$pointFraicheur, $monAvis]) : route('points-fraicheur.avis.store', $pointFraicheur) }}">
                                @csrf
                                @if ($monAvis)
                                    @method('PUT')
                                @endif
                                <label class="form-label">Note</label>
                                <select class="form-select" name="note" required>
                                    @for ($note = 1; $note <= 5; $note++)
                                        <option value="{{ $note }}" @selected(old('note', $monAvis?->note) == $note)>{{ $note }}/5</option>
                                    @endfor
                                </select>
                                <label class="form-label mt-3">Commentaire</label>
                                <textarea class="form-control" name="commentaire" rows="4">{{ old('commentaire', $monAvis?->commentaire) }}</textarea>
                                <button class="btn-main mt-3" type="submit">{{ $monAvis ? 'Mettre à jour' : 'Envoyer mon avis' }}</button>
                            </form>
                            @if ($monAvis)
                                <form method="POST" action="{{ route('points-fraicheur.avis.destroy', [$pointFraicheur, $monAvis]) }}" class="mt-3" onsubmit="return confirm('Supprimer votre avis ? Cette action est irréversible.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger w-100">Supprimer mon avis</button>
                                </form>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const pointMap = L.map('point-fraicheur-map').setView([{{ $pointFraicheur->latitude }}, {{ $pointFraicheur->longitude }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
        }).addTo(pointMap);
        L.marker([{{ $pointFraicheur->latitude }}, {{ $pointFraicheur->longitude }}])
            .addTo(pointMap)
            .bindPopup(@json($pointFraicheur->nom));
    </script>
@endsection
