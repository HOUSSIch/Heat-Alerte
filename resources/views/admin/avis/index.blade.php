@extends('admin.layouts.gentelella')

@section('title', 'Avis')

@section('page-title', 'Avis habitants')

@section('breadcrumb')
    <li class="breadcrumb-item active">Avis</li>
@endsection

@section('content')
    @php
        $analysis = session('avis_analysis');
        $sentiment = data_get($analysis, 'sentiment_global', '—');
        $normalizedSentiment = mb_strtolower($sentiment);
        $sentimentClass = str_contains($normalizedSentiment, 'posit')
            ? 'success'
            : (str_contains($normalizedSentiment, 'négat') || str_contains($normalizedSentiment, 'negat')
                ? 'danger'
                : 'warning');
    @endphp

    <p class="text-muted mb-4">Consultez, analysez et modérez les retours publiés par les habitants.</p>

    <div class="x_panel card mb-4">
        <div class="x_title card-header">
            <h2 class="card-title mb-0">
                Analyse intelligente des avis
                <small class="d-block text-muted fw-normal mt-1">Analyse IA — aide à la décision administrateur</small>
            </h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content card-body">
            <form id="groq-analysis-form" method="POST" action="{{ route('admin.avis.analyse') }}">
                @csrf
                <div class="row align-items-end g-3">
                    <div class="col-12 col-md-5 col-lg-4">
                        <label class="form-label" for="point_fraicheur_id">Point de fraîcheur</label>
                        <select id="point_fraicheur_id" class="form-select" name="point_fraicheur_id">
                            @foreach ($pointsAnalysables as $point)
                                <option value="{{ $point->id }}" @selected($point->nom === 'El Menzah 4')>{{ $point->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-auto">
                        <button id="groq-analysis-button" class="btn btn-primary" type="submit">Analyser les avis avec l’IA</button>
                    </div>
                </div>
            </form>

            <div id="groq-analysis-result">
                @if ($analysis)
                    <div class="well mt-4 mb-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h4 class="mb-1">Analyse IA — {{ session('analysed_point') }}</h4>
                                <span class="text-muted">Lecture synthétique des avis publiés</span>
                            </div>
                            <span class="badge text-bg-{{ $sentimentClass }}">Sentiment global : {{ $sentiment }}</span>
                        </div>
                        <div class="alert alert-info mb-4">
                            <strong>Résumé</strong>
                            <div class="mt-1">{{ data_get($analysis, 'resume', '—') }}</div>
                        </div>
                        <div class="row g-4">
                            <div class="col-12 col-md-6">
                                <h5>Points positifs</h5>
                                <ul class="list-unstyled mb-0" style="margin-left: 2cm;">
                                    @forelse (data_get($analysis, 'points_positifs', []) as $positive)
                                        <li class="py-2 border-bottom">{{ $positive }}</li>
                                    @empty
                                        <li class="text-muted py-2">Aucun point positif identifié.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="col-12 col-md-6">
                                <h5>Problèmes fréquents</h5>
                                <ul class="list-unstyled mb-0">
                                    @forelse (data_get($analysis, 'problemes_frequents', []) as $problem)
                                        <li class="py-2 border-bottom">{{ $problem }}</li>
                                    @empty
                                        <li class="text-muted py-2">Aucun problème fréquent identifié.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="col-12">
                                <h5>Avis à vérifier</h5>
                                <ul class="list-unstyled mb-0" style="margin-left: 2cm;">
                                    @forelse (data_get($analysis, 'avis_a_verifier', []) as $review)
                                        <li class="py-2 border-bottom">
                                            @if (is_array($review))
                                                <strong>Avis #{{ data_get($review, 'id', '—') }}</strong>
                                                @if (data_get($review, 'raison'))
                                                    — {{ data_get($review, 'raison') }}
                                                @endif
                                            @else
                                                {{ $review }}
                                            @endif
                                        </li>
                                    @empty
                                        <li class="text-muted py-2">Aucun avis ne nécessite de vérification.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                        <p class="text-muted fst-italic mb-0 mt-4">
                            Suggestion IA — validation administrateur requise avant toute action de modération.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="x_panel card">
        <div class="x_title card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h2 class="card-title mb-0">Modération des avis</h2>
            <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
                <label class="visually-hidden" for="statut">Statut</label>
                <select id="statut" class="form-select form-select-sm" name="statut">
                    <option value="">Tous les statuts</option>
                    @foreach (['en_attente' => 'En attente', 'publie' => 'Publiés', 'rejete' => 'Rejetés'] as $value => $label)
                        <option value="{{ $value }}" @selected($statut === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-primary" type="submit">Filtrer</button>
                @if ($statut)
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.avis.index') }}">Réinitialiser</a>
                @endif
            </form>
            <div class="clearfix"></div>
        </div>
        <div class="x_content card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover jambo_table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Utilisateur</th><th>Point de fraîcheur</th><th>Note</th><th>Commentaire</th>
                            <th>Statut</th><th>Date</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($avis as $item)
                            <tr>
                                <td>{{ $item->user?->name ?? 'Utilisateur supprimé' }}</td>
                                <td>
                                    {{ $item->pointFraicheur?->nom ?? 'Point supprimé' }}
                                    <div class="small text-secondary">{{ $item->pointFraicheur?->quartier?->nom }}</div>
                                </td>
                                <td>{{ $item->note }}/5</td>
                                <td class="text-wrap" style="min-width: 180px;">{{ $item->commentaire ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ ['en_attente' => 'text-bg-warning', 'publie' => 'text-bg-success', 'rejete' => 'text-bg-danger'][$item->statut] }}">
                                        {{ str_replace('_', ' ', ucfirst($item->statut)) }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.avis.show', $item) }}">Voir</a>
                                    @if ($item->statut !== 'publie')
                                        <form class="d-inline" method="POST" action="{{ route('admin.avis.publish', $item) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-sm btn-outline-success" type="submit">Publier</button>
                                        </form>
                                    @endif
                                    @if ($item->statut !== 'rejete')
                                        <form class="d-inline" method="POST" action="{{ route('admin.avis.reject', $item) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Rejeter</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-secondary py-5">Aucun avis correspondant.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
        }[character]));
        const listItems = (items, emptyMessage, formatter = (item) => escapeHtml(item)) => {
            if (!Array.isArray(items) || items.length === 0) {
                return '<li class="text-muted py-2">' + emptyMessage + '</li>';
            }
            return items.map((item) => '<li class="py-2 border-bottom">' + formatter(item) + '</li>').join('');
        };
        const sentimentClass = (sentiment) => {
            const normalized = String(sentiment ?? '').toLocaleLowerCase('fr');
            if (normalized.includes('posit')) return 'success';
            if (normalized.includes('négat') || normalized.includes('negat')) return 'danger';
            return 'warning';
        };
        const renderAnalysis = (analysis, point) => {
            const sentiment = analysis.sentiment_global || '—';
            const reviews = listItems(analysis.avis_a_verifier, 'Aucun avis ne nécessite de vérification.', (review) => {
                if (typeof review === 'object' && review !== null) {
                    const reason = review.raison ? ' — ' + escapeHtml(review.raison) : '';
                    return '<strong>Avis #' + escapeHtml(review.id || '—') + '</strong>' + reason;
                }
                return escapeHtml(review);
            });
            return '<div class="well mt-4 mb-0">'
                + '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3"><div><h4 class="mb-1">Analyse IA — ' + escapeHtml(point) + '</h4><span class="text-muted">Lecture synthétique des avis publiés</span></div><span class="badge text-bg-' + sentimentClass(sentiment) + '">Sentiment global : ' + escapeHtml(sentiment) + '</span></div>'
                + '<div class="alert alert-info mb-4"><strong>Résumé</strong><div class="mt-1">' + escapeHtml(analysis.resume || '—') + '</div></div>'
                + '<div class="row g-4"><div class="col-12 col-md-6"><h5>Points positifs</h5><ul class="list-unstyled mb-0" style="margin-left: 2cm;">' + listItems(analysis.points_positifs, 'Aucun point positif identifié.') + '</ul></div><div class="col-12 col-md-6"><h5>Problèmes fréquents</h5><ul class="list-unstyled mb-0">' + listItems(analysis.problemes_frequents, 'Aucun problème fréquent identifié.') + '</ul></div><div class="col-12"><h5>Avis à vérifier</h5><ul class="list-unstyled mb-0" style="margin-left: 2cm;">' + reviews + '</ul></div></div>'
                + '<p class="text-muted fst-italic mb-0 mt-4">Suggestion IA — validation administrateur requise avant toute action de modération.</p></div>';
        };
        document.getElementById('groq-analysis-form')?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.currentTarget;
            const button = document.getElementById('groq-analysis-button');
            const result = document.getElementById('groq-analysis-result');
            const label = button.textContent;
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 30000);
            button.disabled = true;
            button.textContent = 'Analyse en cours...';
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': form.querySelector('[name=_token]').value, Accept: 'application/json'},
                    body: new FormData(form),
                    signal: controller.signal,
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message);
                result.innerHTML = renderAnalysis(data.analysis, data.point);
            } catch (error) {
                const message = error.name === 'AbortError'
                    ? 'Le service d’analyse IA met trop de temps à répondre. Veuillez réessayer.'
                    : (error.message || 'Le service d’analyse IA est temporairement indisponible.');
                result.innerHTML = '<div class="alert alert-danger mt-3">' + escapeHtml(message) + '</div>';
            } finally {
                clearTimeout(timer);
                button.disabled = false;
                button.textContent = label;
            }
        });
    </script>
@endpush
