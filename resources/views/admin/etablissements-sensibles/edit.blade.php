@extends('admin.layouts.gentelella')

@section('title', 'Modifier un établissement sensible')

@section('page-title', 'Modifier un établissement sensible')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.etablissements-sensibles.index') }}">Établissements sensibles</a></li>
    <li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
    @include('admin.etablissements-sensibles.form')
@endsection
