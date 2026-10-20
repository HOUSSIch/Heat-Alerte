@extends('admin.layouts.gentelella')

@section('title', $etablissementSensible->nom)

@section('page-title', 'Établissement sensible')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.etablissements-sensibles.index') }}">Établissements sensibles</a></li>
    <li class="breadcrumb-item active">Détail</li>
@endsection

@section('content')
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div>
                        <h3 class="card-title mb-0">{{ $etablissementSensible->nom }}</h3>
                        <div class="small text-secondary">{{ $etablissementSensible->type }}</div>
                    </div>
                    <span class="badge {{ $etablissementSensible->actif ? 'text-bg-success' : 'text-bg-secondary' }}">
                        {{ $etablissementSensible->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Quartier</dt>
                        <dd class="col-sm-8">{{ $etablissementSensible->quartier?->nom ?? '—' }}</dd>
                        <dt class="col-sm-4">Adresse</dt>
                        <dd class="col-sm-8">{{ $etablissementSensible->adresse }}</dd>
                        <dt class="col-sm-4">Téléphone</dt>
                        <dd class="col-sm-8">{{ $etablissementSensible->telephone ?: 'Non renseigné' }}</dd>
                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $etablissementSensible->description ?: 'Aucune description.' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body d-grid gap-2">
                    <a class="btn btn-outline-primary"
                        href="{{ route('admin.etablissements-sensibles.edit', $etablissementSensible) }}">Modifier</a>
                    <form method="POST" action="{{ route('admin.etablissements-sensibles.destroy', $etablissementSensible) }}"
                        onsubmit="return confirm('Supprimer cet établissement sensible ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger w-100">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
