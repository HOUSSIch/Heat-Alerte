@extends('admin.layouts.gentelella')

@section('title', 'Établissements sensibles')

@section('page-title', 'Établissements sensibles')

@section('breadcrumb')
    <li class="breadcrumb-item active">Établissements sensibles</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <div>
                <h3 class="card-title mb-0">Établissements sensibles</h3>
                <div class="small text-secondary">Structures nécessitant une attention particulière pendant les fortes chaleurs.</div>
            </div>
            <div class="card-tools">
                <a class="btn btn-primary btn-sm" href="{{ route('admin.etablissements-sensibles.create') }}">
                    Ajouter un établissement
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Quartier</th>
                        <th>Adresse</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($etablissements as $etablissementSensible)
                        <tr>
                            <td>
                                <a class="fw-semibold text-reset text-decoration-none"
                                    href="{{ route('admin.etablissements-sensibles.show', $etablissementSensible) }}">
                                    {{ $etablissementSensible->nom }}
                                </a>
                            </td>
                            <td>{{ $etablissementSensible->type }}</td>
                            <td>{{ $etablissementSensible->quartier?->nom ?? '—' }}</td>
                            <td>{{ $etablissementSensible->adresse }}</td>
                            <td>
                                <span class="badge {{ $etablissementSensible->actif ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $etablissementSensible->actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('admin.etablissements-sensibles.show', $etablissementSensible) }}">Voir</a>
                                <a class="btn btn-sm btn-outline-secondary"
                                    href="{{ route('admin.etablissements-sensibles.edit', $etablissementSensible) }}">Modifier</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                Aucun établissement sensible n’est enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
