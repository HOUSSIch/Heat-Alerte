@extends('admin.layouts.gentelella') @section('title', $coupure->titre) @section('content')<div class="card">
    <div class="card-body">
        <h3>{{ $coupure->titre }}</h3>
        <p>{{ $coupure->quartier?->nom ?? 'Quartier non renseigné' }} · {{ $coupure->statut }}</p>
        <p>{{ $coupure->description }}</p>
        <hr><p><strong>Statut calculé :</strong> {{ $tracking['status'] }}</p><p><strong>Progression :</strong> {{ $tracking['progress'] }} %</p><p><strong>Temps écoulé :</strong> {{ $tracking['elapsed'] ?? '—' }}</p><p><strong>Temps restant / retard :</strong> {{ $tracking['remaining'] ?? '—' }}</p>@if($tracking['actualDuration'])<p><strong>Durée réelle :</strong> {{ $tracking['actualDuration'] }}</p>@endif
    </div>
    <div class="card-footer"><a class="btn btn-primary" href="{{ route('admin.coupures.edit', $coupure) }}">Modifier</a>
    </div>
</div>@endsection
