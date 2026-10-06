@extends('layouts.admin')
@section('title', 'Créer un point de fraîcheur')
@section('page-title', 'Créer un point de fraîcheur')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('admin.points-fraicheur.index') }}">Points de fraîcheur</a></li><li class="breadcrumb-item active">Créer</li>@endsection
@section('content')
    @include('admin.points-fraicheur.form')
@endsection
