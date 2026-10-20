@extends('admin.layouts.gentelella') @section('title', 'Coupures') @section('page-title', 'Coupures électriques') @section('content')<div
    class="card">
    <div class="card-header">
        <h3 class="card-title">Coupures</h3><a class="btn btn-primary btn-sm float-end"
            href="{{ route('admin.coupures.create') }}">Créer</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Titre</th>
                    <th scope="col">Quartier</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coupures as $c)
                    <tr>
                        <td>{{ $c->titre }}</td>
                        <td>{{ $c->quartier?->nom ?? 'Quartier non renseigné' }}</td>
                        <td>{{ $c->statut }}</td>
                        <td><a href="{{ route('admin.coupures.show', $c) }}">Voir</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>@endsection
