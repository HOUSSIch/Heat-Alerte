@extends('layouts.front')

@section('title', 'Signaler une coupure')

@section('content')
    @include('partials.front-page-title', [
        'title' => 'Signaler une coupure',
        'subtitle' => 'Aidez-nous à localiser une panne électrique.',
    ])

    <section class="contact section">
        <div class="container" data-aos="fade-up">
            <div class="row g-5">
                <div class="col-lg-8">
                    <form method="POST" action="{{ route('signalements.store') }}" class="php-email-form heatalert-panel">
                        @csrf
                        <h3>Informations du signalement</h3>

                        <div class="row gy-4">
                            <div class="col-12">
                                <label class="form-label" for="quartier_id">Quartier</label>
                                <select id="quartier_id" class="form-select @error('quartier_id') is-invalid @enderror" name="quartier_id">
                                    @foreach ($quartiers as $quartier)
                                        <option value="{{ $quartier->id }}" @selected(old('quartier_id', $signalement->quartier_id) == $quartier->id)>
                                            {{ $quartier->nom }} — {{ $quartier->ville }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('quartier_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="titre">Titre</label>
                                <input id="titre" class="form-control @error('titre') is-invalid @enderror" name="titre" value="{{ old('titre') }}" required>
                                @error('titre')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="description">Description</label>
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="adresse">Adresse</label>
                                <input id="adresse" list="adresse-results" class="form-control @error('adresse') is-invalid @enderror" name="adresse" value="{{ old('adresse') }}" placeholder="Ex. Bardo, Tunis">
                                <datalist id="adresse-results"></datalist>
                                <p id="address-help" class="mt-2 mb-0">La localisation sera recherchée automatiquement avec OpenStreetMap.</p>
                            </div>

                            <div class="col-12 d-flex gap-3">
                                <a class="btn btn-outline-secondary" href="{{ route('coupures.index') }}">Annuler</a>
                                <button class="btn-main" type="submit">Envoyer le signalement</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <aside class="sidebar">
                        <div class="help-card">
                            <i class="bi bi-stars"></i>
                            <h4>Assistant IA</h4>
                            <p>Décrivez librement votre problème : l’assistant propose des champs que vous pourrez vérifier avant l’envoi.</p>
                            <label class="visually-hidden" for="assistant-text">Description destinée à l’assistant IA</label>
                            <textarea id="assistant-text" class="form-control mb-2" rows="5" placeholder="Plus de courant depuis deux heures..."></textarea>
                            <button id="assistant-fill" class="btn-main" type="button">Remplir avec l’IA</button>
                            <p id="assistant-feedback" class="mt-2 mb-0" aria-live="polite"></p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value;
        let addressTimeout;

        document.getElementById('adresse').addEventListener('input', (event) => {
            clearTimeout(addressTimeout);
            addressTimeout = setTimeout(async () => {
                if (event.target.value.length < 3) {
                    return;
                }

                try {
                    const response = await fetch('{{ route('signalements.addresses') }}?q=' + encodeURIComponent(event.target.value));
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message);
                    }

                    document.getElementById('adresse-results').innerHTML = data.results
                        .map((result) => '<option value="' + result.label + '">')
                        .join('');
                } catch {
                    document.getElementById('address-help').textContent = 'Recherche d’adresse temporairement indisponible.';
                }
            }, 400);
        });

        document.getElementById('assistant-fill').addEventListener('click', async (event) => {
            const startedAt = performance.now();
            const button = event.currentTarget;
            const feedback = document.getElementById('assistant-feedback');
            const label = button.textContent;
            const url = '{{ route('signalements.assistant-fill') }}';
            const text = document.getElementById('assistant-text').value;
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 30000);

            console.log('[AI-SIGNAL] Click detected');
            console.log('[AI-SIGNAL] Request started');
            console.log('[AI-SIGNAL] URL:', url);
            console.log('[AI-SIGNAL] Payload:', { texte: text });
            console.log('[AI-SIGNAL] CSRF present:', Boolean(csrfToken));

            if (!csrfToken) {
                console.error('[AI-SIGNAL] CSRF token missing');
                clearTimeout(timer);
                feedback.textContent = 'Impossible d’envoyer la requête. Rechargez la page.';
                console.log('[AI-SIGNAL] Total frontend time:', Math.round(performance.now() - startedAt), 'ms');

                return;
            }

            button.disabled = true;
            button.textContent = 'Analyse en cours...';
            feedback.textContent = '';

            try {
                console.log('[AI-SIGNAL] Fetch starting');
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ texte: text }),
                    signal: controller.signal,
                });
                console.log('[AI-SIGNAL] Fetch finished');
                console.log('[AI-SIGNAL] Response received');
                console.log('[AI-SIGNAL] HTTP status:', response.status);
                console.log('[AI-SIGNAL] Parsing JSON');
                const data = await response.json();
                console.log('[AI-SIGNAL] JSON parsed', data);
                console.log('[AI-SIGNAL] Response body:', data);

                if (!response.ok) {
                    throw new Error(data.message);
                }

                console.log('[AI-SIGNAL] Applying fields');
                const fieldLabels = {
                    titre: 'title',
                    description: 'description',
                    adresse: 'address',
                    quartier_id: 'quartier',
                };
                ['titre', 'description', 'adresse', 'quartier_id'].forEach((field) => {
                    if (data[field] !== null && data[field] !== undefined) {
                        document.getElementById(field).value = data[field];
                        console.log('[AI-SIGNAL] ' + fieldLabels[field] + ' applied');
                    }
                });

                feedback.textContent = 'Proposition appliquée au formulaire.';
            } catch (error) {
                console.error('[AI-SIGNAL] FETCH ERROR', error);
                console.error('[AI-SIGNAL] Error name:', error.name);
                console.error('[AI-SIGNAL] Error message:', error.message);

                if (error.name === 'AbortError') {
                    console.error('[AI-SIGNAL] Request aborted by timeout');
                }

                feedback.textContent = error.name === 'AbortError'
                    ? 'Le service IA met trop de temps à répondre. Veuillez réessayer.'
                    : (error.message || 'Le service IA de saisie est temporairement indisponible.');
            } finally {
                console.log('[AI-SIGNAL] finally executed');
                clearTimeout(timer);
                button.disabled = false;
                button.textContent = label;
                console.log('[AI-SIGNAL] Button restored');
                console.log('[AI-SIGNAL] Total frontend time:', Math.round(performance.now() - startedAt), 'ms');
            }
        });
    </script>
@endpush
