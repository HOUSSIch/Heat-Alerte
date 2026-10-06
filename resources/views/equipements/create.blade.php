@extends('layouts.user')

@section('title', 'Ajouter un équipement - HeatAlert')

@section('content')
    <div class="page-header d-print-none"><div class="row align-items-center"><div class="col"><div class="page-pretitle">Mes équipements</div><h2 class="page-title">Ajouter un équipement</h2><div class="text-secondary">Indiquez les appareils que HeatAlert doit surveiller.</div></div><div class="col-auto"><span class="badge bg-orange-lt text-orange">Protection chaleur</span></div></div></div>
    @include('equipements.form', ['action' => route('equipements.store'), 'method' => 'POST', 'submitLabel' => 'Enregistrer'])
@endsection
