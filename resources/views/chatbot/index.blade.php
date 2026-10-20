@extends('layouts.front')

@section('title', 'Assistant HeatAlert')

@section('content')
    @php
        $initial = strtoupper(substr(auth()->user()->name ?? 'U', 0, 1));
    @endphp

    @include('partials.front-page-title', ['title' => 'Assistant HeatAlert', 'subtitle' => 'Posez vos questions sur vos équipements, la chaleur et les coupures.'])

    <section class="contact section">
        <div class="container" data-aos="fade-up">
            <div class="d-flex justify-content-end mb-4"><form method="POST" action="{{ route('chatbot.clear') }}">@csrf @method('DELETE')<button class="btn btn-outline-secondary" type="submit">Effacer l’historique</button></form></div>
    <div class="heatalert-panel">
        <div class="d-flex align-items-center border-bottom pb-3">
            <i class="bi bi-chat-dots heatalert-icon me-3"></i>
            <div>
                <h3 class="card-title mb-0">Assistant HeatAlert</h3>
                <div class="small text-secondary"><span class="badge bg-green me-1">En ligne</span> Disponible pour vous
                    accompagner</div>
            </div>
        </div>

        <div class="heatalert-chat p-4 mt-4" style="max-height: 520px; overflow-y: auto;">
            @forelse ($history as $message)
                @if ($message['role'] === 'user')
                    <div class="d-flex justify-content-end align-items-end gap-2 mb-3">
                        <div class="bg-blue-lt rounded-4 px-3 py-2 text-body text-break" style="max-width: 70%;">
                            {!! nl2br(e($message['content'])) !!}
                        </div>
                        <span class="avatar avatar-sm bg-blue-lt text-blue flex-shrink-0">{{ $initial }}</span>
                    </div>
                @else
                    <div class="d-flex align-items-end gap-2 mb-3">
                        <span
                            class="avatar avatar-sm bg-orange-lt text-orange flex-shrink-0">@include('partials.tabler-icon', [
                                'name' => 'message',
                                'class' => 'icon icon-1',
                            ])</span>
                        <div class="bg-light border rounded-4 px-3 py-2 text-body text-break" style="max-width: 70%;">
                            {!! nl2br(e($message['content'])) !!}
                        </div>
                    </div>
                @endif
            @empty
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center px-3">
                    <span class="avatar avatar-xl bg-orange-lt text-orange mb-3">@include('partials.tabler-icon', ['name' => 'message', 'class' => 'icon icon-3'])</span>
                    <h3>Bonjour {{ auth()->user()->name }},</h3>
                    <p class="text-secondary mw-100" style="max-width: 540px;">Je peux vous aider à protéger vos équipements
                        et mieux réagir aux fortes chaleurs et aux coupures.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-2" id="assistant-suggestions">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-suggestion="Comment protéger mon réfrigérateur ?">Comment protéger mon réfrigérateur
                            ?</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-suggestion="Que faire pendant une coupure ?">Que faire pendant une coupure ?</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-suggestion="Comment protéger mon PC ?">Comment protéger mon PC ?</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-suggestion="Quels sont mes équipements sensibles ?">Quels sont mes équipements sensibles
                            ?</button>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            <form method="POST" action="{{ route('chatbot.send') }}">
                @csrf
                <div class="input-group">
                    <input id="assistant-question" class="form-control" name="question" maxlength="2000"
                        placeholder="Écrivez votre question..." required>
                    <button class="btn btn-primary" type="submit" aria-label="Envoyer la question">
                        @include('partials.tabler-icon', ['name' => 'message', 'class' => 'icon icon-2'])
                        <span class="d-none d-sm-inline ms-1">Envoyer</span>
                    </button>
                </div>
                @error('question')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </form>
        </div>
    </div></div></section>

    <script>
        document.querySelectorAll('[data-suggestion]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById('assistant-question');
                input.value = button.dataset.suggestion;
                input.focus();
            });
        });
    </script>
@endsection
