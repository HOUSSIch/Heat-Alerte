@extends('admin.layouts.gentelella')

@section('title', 'Suivi équipements')

@section('page-title', 'Suivi équipements')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Équipements déclarés</div>
                <div class="card-subtitle">Consultation globale et suivi des entretiens.</div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="admin-filter-grid">
                <div><label for="search">Recherche</label><input id="search" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nom, marque, utilisateur…"></div>
                <div><label for="user_id">Utilisateur</label><select id="user_id" class="form-select" name="user_id"><option value="">Tous</option>@foreach ($users as $user)<option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }} · {{ $user->email }}</option>@endforeach</select></div>
                <div><label for="type">Type</label><select id="type" class="form-select" name="type"><option value="">Tous</option>@foreach ($types as $type)<option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>@endforeach</select></div>
                <div><label for="statut_entretien">Statut entretien</label><select id="statut_entretien" class="form-select" name="statut_entretien"><option value="">Tous</option><option value="à_jour" @selected(request('statut_entretien') === 'à_jour')>À jour</option><option value="prévu" @selected(request('statut_entretien') === 'prévu')>Entretien prévu</option><option value="en_retard" @selected(request('statut_entretien') === 'en_retard')>En retard</option><option value="aucun_entretien" @selected(request('statut_entretien') === 'aucun_entretien')>Aucun entretien</option></select></div>
                <div><label for="sensible_chaleur">Sensible chaleur</label><select id="sensible_chaleur" class="form-select" name="sensible_chaleur"><option value="">Tous</option><option value="1" @selected(request('sensible_chaleur') === '1')>Oui</option><option value="0" @selected(request('sensible_chaleur') === '0')>Non</option></select></div>
                <div><label for="sensible_coupure">Sensible coupure</label><select id="sensible_coupure" class="form-select" name="sensible_coupure"><option value="">Tous</option><option value="1" @selected(request('sensible_coupure') === '1')>Oui</option><option value="0" @selected(request('sensible_coupure') === '0')>Non</option></select></div>
                <div class="admin-filter-actions"><button class="btn btn-primary" type="submit">Filtrer</button><a class="btn" href="{{ route('admin.equipements.index') }}">Réinitialiser</a></div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Référence</th><th>Utilisateur</th><th>Équipement</th><th>Sensibilités</th><th>Entretiens</th><th>Prochain</th><th>Statut</th><th>Créé le</th><th></th></tr></thead>
                <tbody>
                    @forelse ($equipements as $equipement)
                        @php($status = $equipement->maintenanceStatus())
                        <tr>
                            <td class="cell-strong">EQ-{{ str_pad((string) $equipement->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $equipement->user?->name ?? '—' }}<div class="small text-secondary">{{ $equipement->user?->email }}</div></td>
                            <td><strong>{{ $equipement->nom }}</strong><div class="small text-secondary">{{ $equipement->type }}{{ $equipement->marque ? ' · '.$equipement->marque : '' }}</div></td>
                            <td><span class="status {{ $equipement->sensible_chaleur ? 'status-yellow' : 'status-blue' }}">Chaleur {{ $equipement->sensible_chaleur ? 'oui' : 'non' }}</span><span class="status {{ $equipement->sensible_coupure ? 'status-red' : 'status-blue' }}">Coupure {{ $equipement->sensible_coupure ? 'oui' : 'non' }}</span></td>
                            <td>{{ $equipement->entretiens_count }}</td>
                            <td>{{ $equipement->nextMaintenance()?->date_prevue?->format('d/m/Y') ?? '—' }}</td>
                            <td><span class="status status-maintenance-{{ $status }}">{{ str_replace('_', ' ', ucfirst($status)) }}</span></td>
                            <td>{{ $equipement->created_at->format('d/m/Y') }}</td>
                            <td><a class="btn btn-outline btn-sm" href="{{ route('admin.equipements.show', $equipement) }}">Consulter</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-secondary">Aucun équipement ne correspond aux filtres.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($equipements->hasPages())<div class="card-body">{{ $equipements->links() }}</div>@endif
    </div>
@endsection
