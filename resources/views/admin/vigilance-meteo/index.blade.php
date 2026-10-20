@extends('admin.layouts.gentelella')

@section('title', 'Centre de vigilance météo')
@section('page-title', 'Centre de vigilance météo')

@section('content')
    <div class="card"><div class="card-header"><div><div class="card-title">Prévisions Open-Meteo sur 7 jours</div><div class="card-subtitle">Actualisation et surveillance par quartier.</div></div></div><div class="table-responsive"><table class="table"><thead><tr><th>Quartier</th><th>Actuelle</th><th>Max aujourd’hui</th><th>Max demain</th><th>Niveau</th><th>Habitants</th><th>Action</th></tr></thead><tbody>@forelse($rows as $row)<tr><td>{{ $row['quartier']->nom }}</td>@if($row['vigilance'])<td>{{ $row['vigilance']['temperature'] }}°C</td><td>{{ $row['vigilance']['days'][0]['temperature_max'] }}°C</td><td>{{ $row['vigilance']['days'][1]['temperature_max'] ?? '—' }}°C</td><td><span class="status status-maintenance-{{ strtolower($row['vigilance']['niveau']) }}">{{ $row['vigilance']['niveau'] }}</span></td>@else<td colspan="4" class="text-danger">Données indisponibles</td>@endif<td>{{ $row['quartier']->users_count }}</td><td><form method="POST" action="{{ route('admin.vigilance-meteo.refresh', $row['quartier']) }}">@csrf<button class="btn btn-primary btn-sm" type="submit">Actualiser</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-secondary">Aucun quartier actif.</td></tr>@endforelse</tbody></table></div></div>
@endsection
