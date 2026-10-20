@extends('admin.layouts.gentelella')
@section('title', $pointFraicheur->nom)
@section('page-title', 'Point de fraîcheur')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('admin.points-fraicheur.index') }}">Points de
            fraîcheur</a></li>
<li class="breadcrumb-item active">Détail</li>@endsection
@section('content')
    @if(session('avis_analysis'))<div class="card mb-3"><div class="card-header"><h3 class="card-title">Analyse IA des avis</h3></div><div class="card-body"><p><strong>Sentiment :</strong> {{ data_get(session('avis_analysis'),'sentiment_global','—') }}</p><p><strong>Résumé :</strong> {{ data_get(session('avis_analysis'),'resume','—') }}</p><p><strong>Points positifs :</strong> {{ implode(', ', data_get(session('avis_analysis'),'points_positifs',[])) }}</p><p><strong>Problèmes fréquents :</strong> {{ implode(', ', data_get(session('avis_analysis'),'problemes_frequents',[])) }}</p><p class="text-muted">Suggestion IA — validation administrateur requise.</p></div></div>@endif
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div>
                        <h3 class="card-title mb-0">{{ $pointFraicheur->nom }}</h3>
                        <div class="small text-secondary">{{ $pointFraicheur->type }} · {{ $pointFraicheur->quartier?->nom }}
                        </div>
                    </div><span
                        class="badge {{ $pointFraicheur->source === 'geoapify' ? 'text-bg-info' : 'text-bg-secondary' }}">{{ $pointFraicheur->source === 'geoapify' ? 'GEOAPIFY' : 'Manuel' }}</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Adresse</dt>
                        <dd class="col-sm-8">{{ $pointFraicheur->adresse }}</dd>
                        <dt class="col-sm-4">Coordonnées</dt>
                        <dd class="col-sm-8">{{ $pointFraicheur->latitude }}, {{ $pointFraicheur->longitude }}</dd>
                        <dt class="col-sm-4">Horaires</dt>
                        <dd class="col-sm-8">{{ $pointFraicheur->horaires ?: 'Non renseignés' }}</dd>
                        <dt class="col-sm-4">Téléphone</dt>
                        <dd class="col-sm-8">{{ $pointFraicheur->telephone ?: 'Non renseigné' }}</dd>
                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $pointFraicheur->description ?: 'Aucune description.' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">Services</h3>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">Climatisé :
                        <strong>{{ $pointFraicheur->climatise ? 'Oui' : 'Non' }}</strong></div>
                    <div class="list-group-item">Eau disponible :
                        <strong>{{ $pointFraicheur->eau_disponible ? 'Oui' : 'Non' }}</strong></div>
                    <div class="list-group-item">Accessible PMR :
                        <strong>{{ $pointFraicheur->accessible_pmr ? 'Oui' : 'Non' }}</strong></div>
                    <div class="list-group-item">Avis reçus : <strong>{{ $pointFraicheur->avis_count }}</strong></div>
                </div>
                <div class="card-footer"><a class="btn btn-outline-primary"
                        href="{{ route('admin.points-fraicheur.edit', $pointFraicheur) }}">Modifier</a>
                    <form class="d-inline" method="POST"
                        action="{{ route('admin.points-fraicheur.destroy', $pointFraicheur) }}"
                        onsubmit="return confirm('Supprimer ce point de fraîcheur ?')">@csrf @method('DELETE')<button
                    class="btn btn-outline-danger">Supprimer</button></form><form class="d-inline" method="POST" action="{{ route('admin.points-fraicheur.analyse-avis',$pointFraicheur) }}">@csrf<button class="btn btn-primary" onclick="this.disabled=true;this.textContent='Analyse en cours...'">Analyser les avis avec l’IA</button></form>
                </div>
            </div>
        </div>
    </div>
@endsection
