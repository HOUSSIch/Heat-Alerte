@extends('layouts.front')

@section('title', $equipement->nom . ' - HeatAlert')

@section('content')
    @php
        $equipmentType = strtolower($equipement->type . ' ' . $equipement->nom);
        $equipmentIcon = str_contains($equipmentType, 'clim') || str_contains($equipmentType, 'réfrig')
            ? 'bi-snow'
            : (str_contains($equipmentType, 'ordinateur') || str_contains($equipmentType, 'pc')
                ? 'bi-laptop'
                : (str_contains($equipmentType, 'télé') ? 'bi-display' : (str_contains($equipmentType, 'ventil') ? 'bi-fan' : 'bi-cpu')));
    @endphp

    @include('partials.front-page-title', ['title' => 'Mon équipement', 'subtitle' => $equipement->nom])

    <section id="service-details" class="service-details section">
        <div class="container" data-aos="fade-up">
            <div class="row g-5">
                <div class="col-lg-4">
                    <aside class="sidebar">
                        <div class="services-list">
                            <h4>Actions</h4>
                            <ul>
                                <li class="active"><a href="{{ route('equipements.edit', $equipement) }}">Modifier <i class="bi bi-pencil"></i></a></li>
                                <li><a href="{{ route('equipements.index') }}">Retour à la liste <i class="bi bi-arrow-left"></i></a></li>
                            </ul>
                        </div>
                        <div class="help-card">
                            <i class="bi bi-shield-exclamation"></i>
                            <h4>Protection</h4>
                            <p>Consultez les conseils associés pour protéger cet équipement.</p>
                        </div>
                    </aside>
                </div>

                <div class="col-lg-8">
                    <article class="detail heatalert-detail-content">
                        @if ($equipement->photo)
                            <img class="equipment-detail-photo" src="{{ asset('storage/' . $equipement->photo) }}" alt="{{ $equipement->nom }}">
                            <div class="heatalert-detail-heading">
                                <h2>{{ $equipement->nom }}</h2>
                                <p class="lead">{{ $equipement->description ?: 'Équipement suivi dans votre espace HeatAlert.' }}</p>
                            </div>
                        @else
                            <div class="heatalert-detail-heading--compact">
                                <span class="heatalert-icon"><i class="bi {{ $equipmentIcon }}"></i></span>
                                <div>
                                    <p>{{ $equipement->type }}{{ $equipement->marque ? ' · ' . $equipement->marque : '' }}</p>
                                    <h2>{{ $equipement->nom }}</h2>
                                </div>
                            </div>
                            <p class="lead">{{ $equipement->description ?: 'Équipement suivi dans votre espace HeatAlert.' }}</p>
                        @endif

                        <h3>Caractéristiques</h3>
                        <div class="row g-3 includes">
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Type : {{ $equipement->type }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Marque : {{ $equipement->marque ?: 'Non renseignée' }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Sensible à la chaleur : {{ $equipement->sensible_chaleur ? 'Oui' : 'Non' }}</div></div>
                            <div class="col-md-6"><div class="inc"><i class="bi bi-check-circle-fill"></i> Sensible aux coupures : {{ $equipement->sensible_coupure ? 'Oui' : 'Non' }}</div></div>
                        </div>

                        <h3>Conseils associés</h3>
                        @forelse ($equipement->conseils as $conseil)
                            <div class="inc mb-3"><i class="bi bi-lightbulb"></i><div><strong>{{ $conseil->titre }}</strong><br>{{ $conseil->description }}</div></div>
                        @empty
                            <p>Aucun conseil associé à cet équipement.</p>
                        @endforelse

                        <div class="equipment-advanced-grid">
                            <section class="equipment-qr-card">
                                <div>
                                    <p class="profile-kicker">Accès rapide</p>
                                    <h3>QR Code de l’équipement</h3>
                                    <p>Affichez le passeport technique, téléchargez son QR ou sa fiche PDF.</p>
                                    <button class="profile-secondary-action qr-open-button" type="button" data-bs-toggle="modal" data-bs-target="#qr-modal-{{ $equipement->id }}"><i class="bi bi-qr-code"></i> Afficher le QR Code</button>
                                </div>
                                <i class="bi bi-qr-code equipment-qr-placeholder"></i>
                            </section>

                            <section class="equipment-ai-card">
                                <p class="profile-kicker">Assistant HeatAlert</p>
                                <h3>Analyser avec l’IA</h3>
                                <p>Obtenez une synthèse liée à cet équipement, ses conseils et ses entretiens.</p>
                                <form action="{{ route('equipements.ai-analysis', $equipement) }}" method="POST">
                                    @csrf
                                    <button class="btn-main" type="submit"><i class="bi bi-stars"></i> Lancer l’analyse</button>
                                </form>
                                @if (session('ai_analysis'))
                                    <div class="equipment-ai-result">{!! nl2br(e(session('ai_analysis'))) !!}</div>
                                @endif
                            </section>
                        </div>

                        <section class="equipment-maintenance-section">
                            @php
                                $prochainEntretien = $equipement->entretiens
                                    ->filter(fn ($entretien) => ! $entretien->date_realisee && $entretien->date_prevue->gte(today()))
                                    ->sortBy('date_prevue')
                                    ->first();
                                $dernierEntretien = $equipement->entretiens
                                    ->filter(fn ($entretien) => $entretien->date_realisee)
                                    ->sortByDesc('date_realisee')
                                    ->first();
                                $entretiensRealises = $equipement->entretiens->filter(fn ($entretien) => $entretien->date_realisee)->count();
                            @endphp
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                                <div>
                                    <p class="profile-kicker">Entretien et suivi</p>
                                    <h3>Entretien et suivi</h3>
                                </div>
                                <a class="btn-main" href="{{ route('equipements.entretiens.create', $equipement) }}"><i class="bi bi-plus-lg"></i> Ajouter un entretien</a>
                            </div>

                            <div class="maintenance-summary">
                                <div><span>Prochain entretien</span><strong>{{ $prochainEntretien ? $prochainEntretien->date_prevue->format('d/m/Y') : 'Aucun planifié' }}</strong></div>
                                <div><span>Dernier entretien</span><strong>{{ $dernierEntretien ? $dernierEntretien->date_realisee->format('d/m/Y') : 'Aucun réalisé' }}</strong></div>
                                <div><span>Entretiens réalisés</span><strong>{{ $entretiensRealises }}</strong></div>
                            </div>

                            @if ($rappelsEntretien->isNotEmpty())
                                <div class="maintenance-reminders" aria-label="Rappels d’entretien">
                                    @foreach ($rappelsEntretien as $rappel)
                                        <article class="maintenance-reminder-card maintenance-reminder-card--{{ $rappel->reminderStatus() }}">
                                            <i class="bi {{ $rappel->reminderStatus() === 'overdue' ? 'bi-exclamation-triangle-fill' : 'bi-bell-fill' }}"></i>
                                            <div>
                                                <p class="maintenance-reminder-title">{{ $rappel->reminderTitle() }}</p>
                                                <h4>{{ $rappel->titre }}</h4>
                                                <p>{{ $rappel->reminderDescription() }}</p>
                                                <span>Date prévue : {{ $rappel->date_prevue->format('d/m/Y') }}</span>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif

                            <h3 class="maintenance-history-title">Historique des entretiens</h3>

                            @forelse ($equipement->entretiens as $entretien)
                                <article class="maintenance-timeline-item">
                                    <div class="maintenance-date">{{ $entretien->date_prevue->format('d/m/Y') }}</div>
                                    <div class="maintenance-content">
                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                            <div><h4>{{ $entretien->titre }}</h4><p>{{ $entretien->type }}{{ $entretien->description ? ' · ' . $entretien->description : '' }}</p></div>
                                            <span class="maintenance-status maintenance-status--{{ $entretien->statut() }}">{{ str_replace('_', ' ', ucfirst($entretien->statut())) }}</span>
                                        </div>
                                        @if ($entretien->date_realisee)<p class="maintenance-done">Réalisé le {{ $entretien->date_realisee->format('d/m/Y') }}</p>@endif
                                        <div class="d-flex gap-3 flex-wrap mt-2">
                                            <a class="profile-secondary-action" href="{{ route('equipements.entretiens.edit', [$equipement, $entretien]) }}">Modifier</a>
                                            @if (! $entretien->date_realisee)<form method="POST" action="{{ route('equipements.entretiens.realiser', [$equipement, $entretien]) }}">@csrf @method('PATCH')<button class="maintenance-action" type="submit">Marquer comme réalisé</button></form>@endif
                                            <form method="POST" action="{{ route('equipements.entretiens.destroy', [$equipement, $entretien]) }}" onsubmit="return confirm('Supprimer cet entretien ?')">@csrf @method('DELETE')<button class="maintenance-delete" type="submit">Supprimer</button></form>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="heatalert-panel text-center"><i class="bi bi-tools heatalert-icon mb-3"></i><h4>Aucun entretien planifié</h4><p class="mb-0">Ajoutez un entretien pour recevoir un rappel et conserver son historique.</p></div>
                            @endforelse
                        </section>

                        <form action="{{ route('equipements.destroy', $equipement) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cet équipement ?')" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger">Supprimer l’équipement</button>
                        </form>
                    </article>
                    @include('partials.equipement-qr-modal', ['equipement' => $equipement])
                </div>
            </div>
        </div>
    </section>
@endsection
