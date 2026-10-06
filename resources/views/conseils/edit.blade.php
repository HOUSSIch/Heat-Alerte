@extends('layouts.admin')
@section('title','Modifier un conseil - HeatAlert')
@section('page-title','Modifier le conseil') @section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('conseils.index') }}">Conseils</a></li><li class="breadcrumb-item active">Modification</li>@endsection
@section('content')@include('conseils.form',['action'=>route('conseils.update',$conseil),'method'=>'PUT','label'=>'Enregistrer les modifications'])@endsection
