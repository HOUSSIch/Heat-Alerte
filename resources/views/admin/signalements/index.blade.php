@extends('admin.layouts.gentelella') @section('title', 'Signalements') @section('content')<div class="card">
    <div class="card-header">
        <h3 class="card-title">Signalements</h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Titre</th>
                <th scope="col">Habitant</th>
                <th scope="col">Statut</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($signalements as $s)
                <tr>
                    <td>{{ $s->titre }}</td>
                    <td>{{ $s->user->name }}</td>
                    <td>{{ $s->statut }}</td>
                    <td><a href="{{ route('admin.signalements.show', $s) }}">Gérer</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>@endsection
