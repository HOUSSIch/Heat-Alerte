@extends('layouts.front')
@section('title', 'Modifier un équipement - HeatAlert')
@section('content')
    @include('partials.front-page-title', ['title' => 'Modifier l’équipement', 'subtitle' => 'Mettez à jour les informations et sensibilités de ' . $equipement->nom . '.'])
    @include('equipements.form', ['action' => route('equipements.update', $equipement), 'method' => 'PUT', 'submitLabel' => 'Enregistrer les modifications'])
@endsection
