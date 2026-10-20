@extends('layouts.front')
@section('title', 'Ajouter un équipement - HeatAlert')
@section('content')
    @include('partials.front-page-title', ['title' => 'Ajouter un équipement', 'subtitle' => 'Indiquez les appareils que HeatAlert doit surveiller.'])
    @include('equipements.form', ['action' => route('equipements.store'), 'method' => 'POST', 'submitLabel' => 'Enregistrer l’équipement'])
@endsection
