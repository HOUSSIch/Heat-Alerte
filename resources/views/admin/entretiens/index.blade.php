@extends('admin.layouts.gentelella')

@section('title', 'Suivi des entretiens')

@section('page-title', 'Suivi des entretiens')

@section('content')
    <div class="card">
        <div class="card-header"><div><div class="card-title">Entretiens des équipements</div><div class="card-subtitle">Surveillance globale en lecture seule.</div></div></div>
        <div class="card-body">
            <form method="GET" class="admin-filter-grid">
                <div><label for="statut">Statut</label><select id="statut" class="form-select" name="statut"><option value="">Tous</option><option value="prévu" @selected(request('statut') === 'prévu')>Prévu</option><option value="réalisé" @selected(request('statut') === 'réalisé')>Réalisé</option><option value="en_retard" @selected(request('statut') === 'en_retard')>En retard</option></select></div>
                <div><label for="equipement_id">Équipement</label><select id="equipement_id" class="form-select" name="equipement_id"><option value="">Tous</option>@foreach($equipements as $equipement)<option value="{{ $equipement->id }}" @selected((string) request('equipement_id') === (string) $equipement->id)>{{ $equipement->nom }}</option>@endforeach</select></div>
                <div><label for="user_id">Utilisateur</label><select id="user_id" class="form-select" name="user_id"><option value="">Tous</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }} · {{ $user->email }}</option>@endforeach</select></div>
                <div><label for="date_from">À partir du</label><input id="date_from" class="form-control" type="date" name="date_from" value="{{ request('date_from') }}"></div>
                <div><label for="date_to">Jusqu’au</label><input id="date_to" class="form-control" type="date" name="date_to" value="{{ request('date_to') }}"></div>
                <div class="admin-filter-actions"><button class="btn btn-primary" type="submit">Filtrer</button><a class="btn" href="{{ route('admin.entretiens.index') }}">Réinitialiser</a></div>
            </form>
        </div>
        <div class="table-responsive"><table class="table"><thead><tr><th>Équipement</th><th>Propriétaire</th><th>Entretien</th><th>Prévu</th><th>Réalisé</th><th>Rappel</th><th>Statut</th></tr></thead><tbody>@forelse($entretiens as $entretien)@php($status = $entretien->date_realisee ? 'réalisé' : ($entretien->reminderStatus() === 'overdue' ? 'en_retard' : 'prévu'))<tr><td><a class="cell-strong" href="{{ route('admin.equipements.show', $entretien->equipement) }}">EQ-{{ str_pad((string) $entretien->equipement_id, 4, '0', STR_PAD_LEFT) }} · {{ $entretien->equipement?->nom }}</a></td><td>{{ $entretien->equipement?->user?->name ?? '—' }}<div class="small text-secondary">{{ $entretien->equipement?->user?->email }}</div></td><td><strong>{{ $entretien->titre }}</strong><div class="small text-secondary">{{ $entretien->type }}</div></td><td>{{ $entretien->date_prevue->format('d/m/Y') }}</td><td>{{ $entretien->date_realisee?->format('d/m/Y') ?? '—' }}</td><td>{{ $entretien->rappel_avant_jours ?? '—' }} jour(s)</td><td><span class="status status-maintenance-{{ $status }}">{{ $status === 'en_retard' ? 'En retard · '.$entretien->date_prevue->diffInDays(today(), true).' j' : ucfirst($status) }}</span></td></tr>@empty<tr><td colspan="7" class="text-center text-secondary">Aucun entretien ne correspond aux filtres.</td></tr>@endforelse</tbody></table></div>
        @if($entretiens->hasPages())<div class="card-body">{{ $entretiens->links() }}</div>@endif
    </div>
@endsection
