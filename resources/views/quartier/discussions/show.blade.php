@extends('layouts.front')

@section('title', $publication->titre.' - HeatAlert')

@section('content')
    @include('partials.front-page-title', ['title' => 'Discussion du quartier', 'subtitle' => $publication->quartier->nom])

    <section class="section">
        <div class="container"><div class="row g-4"><div class="col-lg-8">
            <article class="heatalert-panel discussion-detail">
                <div class="d-flex justify-content-between flex-wrap gap-2"><span class="discussion-type discussion-type-{{ $publication->type }}">{{ ucfirst($publication->type) }}</span><span class="discussion-status status-{{ $publication->statut }}">{{ str_replace('_', ' ', ucfirst($publication->statut)) }}</span></div>
                <h2 class="mt-3">{{ $publication->titre }}</h2>
                <p class="discussion-meta"><i class="bi bi-person-circle"></i> {{ $publication->user?->name ?? 'Habitant' }} · <i class="bi bi-clock"></i> {{ $publication->created_at->diffForHumans() }}</p>
                <div class="discussion-content">{!! nl2br(e($publication->contenu)) !!}</div>
                @if ($publication->statut === 'resolu' && $publication->resolved_at)<p class="mt-3 text-success"><i class="bi bi-check-circle"></i> Résolu le {{ $publication->resolved_at->format('d/m/Y à H:i') }}</p>@endif
                <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
                    <form method="POST" action="{{ route('discussions-quartier.confirmation', $publication) }}">@csrf<button class="discussion-action" type="submit"><i class="bi bi-people"></i> Moi aussi</button></form>
                    <span class="discussion-counts"><i class="bi bi-people"></i> {{ $publication->confirmations_count }} habitant(s) concerné(s)</span>
                    @if ($publication->user_id === auth()->id())
                        <form method="POST" action="{{ route('discussions-quartier.status', $publication) }}" class="discussion-status-form">@csrf @method('PATCH')<label class="visually-hidden" for="statut">Statut</label><select id="statut" class="discussion-select" name="statut">@foreach (['signale' => 'Signalé', 'en_cours' => 'En cours', 'resolu' => 'Résolu'] as $value => $label)<option value="{{ $value }}" @selected($publication->statut === $value)>{{ $label }}</option>@endforeach</select><button class="discussion-action discussion-action--secondary" type="submit"><i class="bi bi-arrow-repeat"></i> Mettre à jour</button></form>
                    @endif
                </div>
            </article>

            <section class="heatalert-panel mt-4" id="comments"><h3>Commentaires ({{ $publication->commentaires_count }})</h3>
                @forelse ($publication->commentaires as $commentaire)
                    <div class="discussion-comment"><strong>{{ $commentaire->user?->name ?? 'Habitant' }}</strong><span>{{ $commentaire->created_at->diffForHumans() }}</span><p>{{ $commentaire->contenu }}</p></div>
                @empty <p class="text-muted">Aucun commentaire pour le moment.</p>
                @endforelse
                <form class="mt-4" method="POST" action="{{ route('discussions-quartier.comment', $publication) }}">@csrf<label class="form-label" for="contenu">Ajouter un commentaire</label><textarea id="contenu" class="form-control discussion-input" name="contenu" rows="4" maxlength="3000" required></textarea>@error('contenu')<div class="text-danger small mt-1">{{ $message }}</div>@enderror<button class="discussion-action mt-3" type="submit"><i class="bi bi-send"></i> Publier le commentaire</button></form>
            </section>
        </div><aside class="col-lg-4">
            <div class="heatalert-panel" id="discussion-ai"><h3><i class="bi bi-stars"></i> Résumé IA</h3>
                @if ($publication->commentaires_count >= 2)
                    <p>Une synthèse se base uniquement sur cette discussion et ses commentaires.</p><button id="summary-button" class="discussion-action discussion-action--ai w-100" data-url="{{ route('discussions-quartier.resume-ia', $publication) }}"><i class="bi bi-stars"></i> Résumer cette discussion avec l’IA</button><div id="summary-result" class="mt-3" aria-live="polite"></div>
                @else <p class="mb-0 text-muted">Pas encore assez de commentaires pour générer un résumé utile.</p>
                @endif
            </div>
        </aside></div></div>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('summary-button')?.addEventListener('click', async function () {
            const button = this;
            const result = document.getElementById('summary-result');
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 30000);
            const originalLabel = button.textContent;
            button.disabled = true;
            button.textContent = 'Analyse en cours...';
            try {
                const response = await fetch(button.dataset.url, {method: 'POST', headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken || ''}, signal: controller.signal});
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Le résumé IA est indisponible.');
                const summary = data.summary;
                const points = (summary.points_cles || []).map((point) => '<li>' + escapeHtml(point) + '</li>').join('');
                result.innerHTML = '<div class="discussion-ai-result"><h4>Résumé IA de la discussion</h4><p><strong>Résumé :</strong> ' + escapeHtml(summary.resume) + '</p><p><strong>Problème principal :</strong> ' + escapeHtml(summary.probleme_principal) + '</p><p><strong>Situation :</strong> ' + escapeHtml(summary.situation) + '</p><ul>' + points + '</ul><small>Résumé généré automatiquement à partir de cette discussion. Les informations peuvent évoluer.</small></div>';
            } catch (error) {
                const message = error.name === 'AbortError' ? 'Le service IA met trop de temps à répondre. Veuillez réessayer.' : (error.message || 'Le résumé IA est temporairement indisponible.');
                result.innerHTML = '<div class="alert alert-danger mt-3">' + escapeHtml(message) + '</div>';
            } finally {
                clearTimeout(timer);
                button.disabled = false;
                button.textContent = originalLabel;
            }
        });
        function escapeHtml(value) { return String(value || '').replace(/[&<>"']/g, (character) => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[character])); }
    </script>
@endpush
