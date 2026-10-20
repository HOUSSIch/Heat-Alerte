@extends('admin.layouts.gentelella')

@section('title', $equipement->nom)

@section('page-title', 'Détail équipement')

@section('content')
    @php($status = $equipement->maintenanceStatus())
    <div class="row col-8-4">
        <div class="card">
            <div class="card-header"><div><div class="card-title">EQ-{{ str_pad((string) $equipement->id, 4, '0', STR_PAD_LEFT) }} — {{ $equipement->nom }}</div><div class="card-subtitle">Consultation administrative en lecture seule.</div></div><span class="status status-maintenance-{{ $status }}">{{ str_replace('_', ' ', ucfirst($status)) }}</span></div>
            <div class="card-body admin-detail-grid">
                @if ($equipement->photo)<img class="admin-equipment-photo" src="{{ asset('storage/'.$equipement->photo) }}" alt="{{ $equipement->nom }}">@endif
                <dl><dt>Type</dt><dd>{{ $equipement->type }}</dd><dt>Marque</dt><dd>{{ $equipement->marque ?: 'Non renseignée' }}</dd><dt>Description</dt><dd>{{ $equipement->description ?: 'Aucune description.' }}</dd><dt>Sensible à la chaleur</dt><dd>{{ $equipement->sensible_chaleur ? 'Oui' : 'Non' }}</dd><dt>Sensible aux coupures</dt><dd>{{ $equipement->sensible_coupure ? 'Oui' : 'Non' }}</dd><dt>Entretiens</dt><dd>{{ $equipement->entretiens->count() }}</dd></dl>
            </div>
            <div class="card-footer"><a class="btn" href="{{ route('admin.equipements.index') }}">Retour à la liste</a></div>
        </div>
        <div>
            <div class="card"><div class="card-header"><div class="card-title">Propriétaire</div></div><div class="card-body"><strong>{{ $equipement->user?->name ?? 'Utilisateur supprimé' }}</strong><div class="text-secondary">{{ $equipement->user?->email }}</div></div></div>
            <div class="card"><div class="card-header"><div class="card-title">Suivi entretien</div></div><div class="card-body"><div class="version-row"><span>Dernier entretien</span><strong>{{ $equipement->lastMaintenance()?->date_realisee?->format('d/m/Y') ?? '—' }}</strong></div><div class="version-row"><span>Prochain entretien</span><strong>{{ $equipement->nextMaintenance()?->date_prevue?->format('d/m/Y') ?? '—' }}</strong></div></div></div>
        </div>
    </div>
    <div class="row col-8-4">
        <div class="card"><div class="card-header"><div><div class="card-title">Historique des entretiens</div><div class="card-subtitle">Aucune action de modification n’est disponible côté administration.</div></div></div><div class="table-responsive"><table class="table"><thead><tr><th>Titre</th><th>Type</th><th>Prévu</th><th>Réalisé</th><th>Rappel</th><th>Statut</th></tr></thead><tbody>@forelse ($equipement->entretiens as $entretien)<tr><td>{{ $entretien->titre }}</td><td>{{ $entretien->type }}</td><td>{{ $entretien->date_prevue->format('d/m/Y') }}</td><td>{{ $entretien->date_realisee?->format('d/m/Y') ?? '—' }}</td><td>{{ $entretien->rappel_avant_jours ?? '—' }} jour(s)</td><td><span class="status status-maintenance-{{ $entretien->date_realisee ? 'réalisé' : ($entretien->reminderStatus() === 'overdue' ? 'en_retard' : 'prévu') }}">{{ $entretien->date_realisee ? 'Réalisé' : ($entretien->reminderStatus() === 'overdue' ? 'En retard' : 'Prévu') }}</span></td></tr>@empty<tr><td colspan="6" class="text-center text-secondary">Aucun entretien.</td></tr>@endforelse</tbody></table></div></div>
        <div class="card"><div class="card-header"><div class="card-title">Conseils associés</div></div><ul class="list-group">@forelse($equipement->conseils as $conseil)<li class="list-group-item"><strong>{{ $conseil->titre }}</strong><div class="small text-secondary">{{ $conseil->categorie }} · {{ $conseil->niveau }}</div></li>@empty<li class="list-group-item text-secondary">Aucun conseil associé.</li>@endforelse</ul></div>
    </div>
@endsection
