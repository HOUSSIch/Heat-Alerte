@extends('layouts.admin')
@section('title','Ajouter un conseil - HeatAlert')
@section('page-title','Ajouter un conseil') @section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('conseils.index') }}">Conseils</a></li><li class="breadcrumb-item active">Ajout</li>@endsection
@section('content')@include('conseils.form',['action'=>route('conseils.store'),'method'=>'POST','label'=>'Enregistrer'])@endsection
