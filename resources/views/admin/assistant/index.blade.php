@extends('admin.layouts.gentelella')

@section('title', 'Assistant HeatAlert')

@section('page-title', 'Assistant HeatAlert')

@section('content')
    <div class="row col-8-4">
        <div class="card">
            <div class="card-header"><div><div class="card-title">Assistant IA Administrateur</div><div class="card-subtitle">Analyse synthétique des équipements, entretiens et conseils.</div></div></div>
            <div class="card-body admin-chat-history">
                @forelse($history as $message)
                    <article class="admin-chat-message admin-chat-message--{{ $message['role'] }}"><strong>{{ $message['role'] === 'user' ? 'Vous' : 'Assistant HeatAlert' }}</strong><p>{{ $message['content'] }}</p></article>
                @empty
                    <p class="text-secondary">Posez une question, par exemple : « Quels équipements ont un entretien en retard ? »</p>
                @endforelse
            </div>
            <div class="card-body"><form method="POST" action="{{ route('admin.assistant.send') }}">@csrf<label for="question">Votre question</label><textarea id="question" class="form-control" name="question" rows="4" required maxlength="2000" placeholder="Ex. Résume-moi les entretiens en retard."></textarea><div class="admin-chat-actions"><button class="btn btn-primary" type="submit">Envoyer à l’assistant</button></div></form></div>
        </div>
        <div class="card"><div class="card-header"><div class="card-title">Contexte utilisé</div></div><div class="card-body"><p class="text-secondary">L’assistant reçoit uniquement une synthèse : statistiques, répartition par type, entretiens prioritaires et conseils actifs.</p><p class="text-secondary">Les mots de passe, clés API, tokens et sessions ne sont jamais envoyés.</p></div></div>
    </div>
@endsection
