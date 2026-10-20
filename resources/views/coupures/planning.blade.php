@extends('layouts.front')

@section('title', 'Planning des coupures')

@section('content')
    @include('partials.front-page-title', ['title' => 'Planning des coupures', 'subtitle' => 'Les interventions prévues dans votre quartier.'])
    <section class="section"><div class="container"><form class="mb-4"><select name="statut" class="form-select" onchange="this.form.submit()"><option value="">Toutes les coupures</option>@foreach(['Programmée','En cours','Terminée','En retard / prolongée'] as $option)<option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>@endforeach</select></form><div id="coupures-calendar" class="heatalert-panel"></div></div></section>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
        <script>document.addEventListener('DOMContentLoaded', () => new FullCalendar.Calendar(document.getElementById('coupures-calendar'), {initialView: 'dayGridMonth', locale: 'fr', events: @json($events), eventClick: (info) => { if (info.event.url) window.location = info.event.url; }}).render());</script>
    @endpush
@endsection
