@extends('admin.layouts.gentelella') @section('title', $alerte->titre) @section('page-title', 'Alerte météo') @section('content')<div
    class="card card">
    <div class="card-body">
        <h3>{{ $alerte->titre }}</h3>
        <p>{{ $alerte->description }}</p>
        <p>Quartier : {{ $alerte->quartier->nom }} · {{ $alerte->temperature }}°C · {{ $alerte->niveau }}</p>
    </div>
    <div class="card-footer"><a class="btn btn-primary" href="{{ route('admin.alertes-meteo.edit', $alerte) }}">Modifier</a>
        <form class="d-inline float-end" method="POST" action="{{ route('admin.alertes-meteo.destroy', $alerte) }}" onsubmit="return confirm('Supprimer cette alerte météo ? Cette action est irréversible.');">@csrf
            @method('DELETE')<button class="btn btn-danger">Supprimer</button></form>
    </div>
</div>@endsection
