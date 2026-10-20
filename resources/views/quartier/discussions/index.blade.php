@extends('layouts.front')

@section('title', 'Discussions du quartier - HeatAlert')

@section('content')
    @include('partials.front-page-title', ['title' => 'Discussions — '.$quartier->nom, 'subtitle' => 'Échangez avec les habitants de votre quartier.'])

    <section class="section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <p class="post-category mb-1">Communauté locale</p>
                    <h2 class="mb-0">Les discussions de {{ $quartier->nom }}</h2>
                </div>
                <a class="discussion-action" href="{{ route('discussions-quartier.create') }}"><i class="bi bi-plus-circle"></i> Nouvelle discussion</a>
            </div>

            <form class="heatalert-filter-bar mb-4" method="GET">
                <select name="type" aria-label="Filtrer par type">
                    <option value="">Tous les types</option>
                    @foreach (['probleme' => 'Problèmes', 'information' => 'Informations', 'entraide' => 'Entraide', 'alerte' => 'Alertes'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="statut" aria-label="Filtrer par statut">
                    <option value="">Tous les statuts</option>
                    @foreach (['signale' => 'Signalés', 'en_cours' => 'En cours', 'resolu' => 'Résolus'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('statut') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="tri" aria-label="Trier les discussions">
                    <option value="recent" @selected(request('tri', 'recent') === 'recent')>Plus récent</option>
                    <option value="commentes" @selected(request('tri') === 'commentes')>Plus commenté</option>
                    <option value="confirmes" @selected(request('tri') === 'confirmes')>Plus de « Moi aussi »</option>
                </select>
                <button class="discussion-action discussion-action--secondary" type="submit"><i class="bi bi-funnel"></i> Filtrer</button>
            </form>

            <div class="row gy-4">
                @forelse ($publications as $publication)
                    <div class="col-lg-6" data-aos="fade-up">
                        <article class="heatalert-card discussion-card h-100">
                            <div class="d-flex justify-content-between gap-3 align-items-start">
                                <div>
                                    <span class="discussion-type discussion-type-{{ $publication->type }}">{{ ucfirst($publication->type) }}</span>
                                    <h3 class="mt-3 mb-2">{{ $publication->titre }}</h3>
                                </div>
                                <span class="discussion-status status-{{ $publication->statut }}">{{ str_replace('_', ' ', ucfirst($publication->statut)) }}</span>
                            </div>
                            <p>{{ \Illuminate\Support\Str::limit($publication->contenu, 180) }}</p>
                            <div class="discussion-meta"><span><i class="bi bi-person-circle"></i> {{ $publication->user?->name ?? 'Habitant' }}</span><span><i class="bi bi-clock"></i> {{ $publication->created_at->diffForHumans() }}</span></div>
                            <div class="discussion-counts"><span><i class="bi bi-people"></i> {{ $publication->confirmations_count }} concerné(s)</span><span><i class="bi bi-chat-dots"></i> {{ $publication->commentaires_count }} commentaire(s)</span></div>
                            <a class="readmore stretched-link" href="{{ route('discussions-quartier.show', $publication) }}">Voir la discussion <i class="bi bi-arrow-right"></i></a>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><div class="heatalert-panel text-center"><i class="bi bi-chat-square-text heatalert-icon"></i><h3>Aucune discussion pour le moment</h3><p>Partagez la première information utile avec votre quartier.</p><a class="discussion-action" href="{{ route('discussions-quartier.create') }}">Créer une discussion</a></div></div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
