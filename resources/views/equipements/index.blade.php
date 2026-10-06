@extends('layouts.user')

@section('title', 'Mes équipements - HeatAlert')

@section('content')
    <div class="page-header d-print-none"><div class="row align-items-center"><div class="col"><div class="page-pretitle">Espace habitant</div><h2 class="page-title">Mes équipements</h2><div class="text-secondary mt-1">Gérez les appareils sensibles à la chaleur ou aux coupures électriques.</div></div><div class="col-auto ms-auto d-print-none"><a href="{{ route('equipements.create') }}" class="btn btn-orange">Ajouter un équipement</a></div></div></div>

    @if (session('success'))<div class="alert alert-success" role="alert">{{ session('success') }}</div>@endif

    <div class="card">
        @if ($equipements->isEmpty())
            <div class="empty"><div class="empty-img"><span class="avatar avatar-xl bg-orange-lt text-orange">@include('partials.tabler-icon', ['name' => 'devices', 'class' => 'icon icon-1'])</span></div><p class="empty-title">Aucun équipement enregistré</p><p class="empty-subtitle text-secondary">Ajoutez vos appareils importants pour mieux anticiper les risques de chaleur et de coupure.</p><div class="empty-action"><a href="{{ route('equipements.create') }}" class="btn btn-orange">Ajouter mon premier équipement</a></div></div>
        @else
            <div class="table-responsive"><table class="table table-vcenter card-table"><thead><tr><th>Nom</th><th>Type</th><th>Marque</th><th>Chaleur</th><th>Coupure</th><th class="w-1">Actions</th></tr></thead><tbody>
                @foreach ($equipements as $equipement)
                    <tr><td><a class="text-reset fw-bold" href="{{ route('equipements.show', $equipement) }}">{{ $equipement->nom }}</a></td><td>{{ $equipement->type }}</td><td>{{ $equipement->marque ?: '—' }}</td><td><span class="badge {{ $equipement->sensible_chaleur ? 'bg-orange-lt text-orange' : 'bg-secondary-lt text-secondary' }}">{{ $equipement->sensible_chaleur ? 'Oui' : 'Non' }}</span></td><td><span class="badge {{ $equipement->sensible_coupure ? 'bg-red-lt text-red' : 'bg-secondary-lt text-secondary' }}">{{ $equipement->sensible_coupure ? 'Oui' : 'Non' }}</span></td><td><div class="btn-list flex-nowrap"><a class="btn btn-sm btn-ghost-primary" href="{{ route('equipements.show', $equipement) }}">Voir</a><a class="btn btn-sm btn-ghost-secondary" href="{{ route('equipements.edit', $equipement) }}">Modifier</a><form action="{{ route('equipements.destroy', $equipement) }}" method="POST" onsubmit="return confirm('Supprimer cet équipement ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-ghost-danger" type="submit">Supprimer</button></form></div></td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </div>
@endsection
