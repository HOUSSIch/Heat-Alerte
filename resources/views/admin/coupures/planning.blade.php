@extends('admin.layouts.gentelella')

@section('title', 'Planning des coupures')
@section('page-title', 'Planning des coupures')

@section('content')
    <form class="card card-body mb-3"><div class="row g-2"><div class="col-md-5"><label class="visually-hidden" for="quartier_id">Quartier</label><select id="quartier_id" name="quartier_id" class="form-select"><option value="">Tous les quartiers</option>@foreach($quartiers as $quartier)<option value="{{ $quartier->id }}" @selected(request('quartier_id') == $quartier->id)>{{ $quartier->nom }}</option>@endforeach</select></div><div class="col-md-5"><label class="visually-hidden" for="statut">Statut</label><select id="statut" name="statut" class="form-select"><option value="">Tous les statuts</option>@foreach(['prévue','en_cours','terminée'] as $statut)<option value="{{ $statut }}" @selected(request('statut') === $statut)>{{ ucfirst(str_replace('_', ' ', $statut)) }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-primary w-100">Filtrer</button></div></div></form><div id="admin-coupures-calendar" class="card card-body"></div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js" integrity="sha384-B1OFx8Gy9GjPu8UbUyXbGQpzll9ubAUQ9agInFJ8NnD7nYG1u/CLR+Sqr5yifl4q" crossorigin="anonymous"></script><script>document.addEventListener('DOMContentLoaded', () => new FullCalendar.Calendar(document.getElementById('admin-coupures-calendar'), {initialView:'dayGridMonth', locale:'fr', events:@json($events), eventClick:(info)=>{if(info.event.url)window.location=info.event.url;}}).render());</script>
@endpush
