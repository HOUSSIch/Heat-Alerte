@extends('layouts.user')

@section('title', 'Modifier un équipement - HeatAlert')

@section('content')
    <div class="page-header d-print-none"><div class="row align-items-center"><div class="col"><div class="page-pretitle">Mes équipements</div><h2 class="page-title">Modifier {{ $equipement->nom }}</h2><div class="text-secondary">Mettez à jour ses informations et ses sensibilités.</div></div><div class="col-auto"><a href="{{ route('equipements.show',$equipement) }}" class="btn btn-outline-secondary">Voir la fiche</a></div></div></div>
    @include('equipements.form', ['action' => route('equipements.update', $equipement), 'method' => 'PUT', 'submitLabel' => 'Enregistrer les modifications'])
@endsection
