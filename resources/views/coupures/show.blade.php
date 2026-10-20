@extends('layouts.front')

@section('title', $coupure->titre)

@section('content')
    @include('partials.front-page-title', ['title' => 'Détail de la coupure', 'subtitle' => $coupure->titre])

    <section id="service-details" class="service-details section">
        <div class="container" data-aos="fade-up">
            <div class="row g-5">
                <div class="col-lg-4">
                    <aside class="sidebar">
                        <div class="services-list">
                            <h4>Statut</h4>
                            <ul>
                                <li class="active">
                                    <a href="#">
                                        {{ str_replace('_', ' ', ucfirst($coupure->statut)) }}
                                        <i class="bi bi-lightning-charge"></i>
                                    </a>
                                </li>
                                <li><a href="#">Source : {{ $coupure->source ?: 'Non renseignée' }}</a></li>
                            </ul>
                        </div>
                        <div class="help-card">
                            <i class="bi bi-chat-dots"></i>
                            <h4>Actions utiles</h4>
                            <p>Retrouvez vos signalements ou signalez une nouvelle interruption.</p>
                            <a href="{{ route('signalements.index') }}" class="btn-main">Mes signalements</a>
                        </div>
                    </aside>
                </div>

                <div class="col-lg-8">
                    <article class="detail heatalert-detail-content">
                        @if ($coupure->latitude && $coupure->longitude)
                            <div id="coupure-map" class="heatalert-detail-map"></div>
                        @endif

                        <div class="heatalert-detail-heading">
                            <h2>{{ $coupure->titre }}</h2>
                            <p class="lead">
                                {{ $coupure->description ?: 'Une intervention électrique est signalée dans votre quartier.' }}
                            </p>
                        </div>

                        <h3>Informations</h3>
                        <div class="row g-3 includes">
                            <div class="col-md-6">
                                <div class="inc"><i class="bi bi-check-circle-fill"></i> Quartier : {{ $coupure->quartier?->nom ?? 'Non renseigné' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="inc"><i class="bi bi-check-circle-fill"></i> Type : {{ $coupure->type ?: 'Non renseigné' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="inc"><i class="bi bi-check-circle-fill"></i> Cause : {{ $coupure->cause ?: 'Non renseignée' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="inc"><i class="bi bi-check-circle-fill"></i> Adresse : {{ $coupure->adresse ?: 'Non renseignée' }}</div>
                            </div>
                        </div>

                        @if ($coupure->date_debut || $coupure->date_fin_estimee || $coupure->date_fin_reelle)
                            <h3>Suivi dynamique</h3>
                            <div class="heatalert-panel" data-coupure-tracking data-start="{{ $coupure->date_debut?->toIso8601String() }}" data-end="{{ $coupure->date_fin_estimee?->toIso8601String() }}">
                                <p><strong>Statut :</strong> <span class="badge text-bg-{{ $tracking['status'] === 'En cours' ? 'danger' : ($tracking['status'] === 'En retard / prolongée' ? 'warning' : 'primary') }}" data-tracking-status>{{ $tracking['status'] }}</span></p>
                                <p data-tracking-elapsed>Temps écoulé : {{ $tracking['elapsed'] ? $tracking['elapsed'].' écoulées' : '—' }}</p>
                                <p data-tracking-remaining>{{ $tracking['status'] === 'En retard / prolongée' ? 'Fin estimée dépassée depuis '.$tracking['remaining'] : ($tracking['remaining'] ? $tracking['remaining'].' restantes' : '—') }}</p>
                                @if ($tracking['actualDuration'])<p>Durée réelle : {{ $tracking['actualDuration'] }}</p>@endif
                                <div class="progress" role="progressbar"><div class="progress-bar" data-tracking-progress style="width: {{ $tracking['progress'] }}%">{{ $tracking['progress'] }}%</div></div>
                            </div>
                            <h3>Planning</h3>
                            <div class="steps">
                                @if ($coupure->date_debut)
                                    <div class="step">
                                        <span class="n">01</span>
                                        <div>
                                            <h4>Début</h4>
                                            <p>{{ $coupure->date_debut->format('d/m/Y \à H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($coupure->date_fin_estimee)
                                    <div class="step">
                                        <span class="n">02</span>
                                        <div>
                                            <h4>Fin estimée</h4>
                                            <p>{{ $coupure->date_fin_estimee->format('d/m/Y \à H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if ($coupure->date_fin_reelle)
                                    <div class="step">
                                        <span class="n">03</span>
                                        <div>
                                            <h4>Fin réelle</h4>
                                            <p>{{ $coupure->date_fin_reelle->format('d/m/Y \à H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </article>
                </div>
            </div>
        </div>
    </section>

    @if ($coupure->latitude && $coupure->longitude)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            const coupureMap = L.map('coupure-map').setView([{{ $coupure->latitude }}, {{ $coupure->longitude }}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
            }).addTo(coupureMap);
            L.marker([{{ $coupure->latitude }}, {{ $coupure->longitude }}]).addTo(coupureMap);
        </script>
    @endif
    @push('scripts')
        <script>
            document.querySelectorAll('[data-coupure-tracking]').forEach((element) => {
                const update = () => {
                    const start = new Date(element.dataset.start); const end = new Date(element.dataset.end); const now = new Date();
                    if (Number.isNaN(start) || Number.isNaN(end) || now < start) return;
                    const overdue = now > end; const progress = Math.min(100, Math.max(0, Math.round((now - start) / (end - start) * 100)));
                    element.querySelector('[data-tracking-progress]').style.width = `${progress}%`; element.querySelector('[data-tracking-progress]').textContent = `${progress}%`;
                    if (overdue) { element.querySelector('[data-tracking-status]').textContent = 'En retard / prolongée'; element.querySelector('[data-tracking-remaining]').textContent = `Fin estimée dépassée depuis ${Math.floor((now - end) / 60000)} min`; }
                }; update(); setInterval(update, 60000);
            });
        </script>
    @endpush
@endsection
