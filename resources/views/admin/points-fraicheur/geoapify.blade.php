@extends('admin.layouts.gentelella')

@section('title', 'Recherche Geoapify')

@section('page-title', 'Rechercher avec Geoapify')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.points-fraicheur.index') }}">Points de fraîcheur</a>
    </li>
    <li class="breadcrumb-item active">Geoapify</li>
@endsection

@section('content')
    <div class="card shadow-sm col-xl-8">
        <div class="card-header">
            <h3 class="card-title mb-0">Suggestions de lieux proches</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                Geoapify propose des lieux à examiner. Ils ne deviennent officiels qu’après votre import.
            </div>
            @error('geoapify')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <form method="POST" action="{{ route('admin.points-fraicheur.geoapify.search') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="quartier_id">Quartier</label>
                        <select class="form-select @error('quartier_id') is-invalid @enderror" id="quartier_id"
                            name="quartier_id" required>
                            @foreach ($quartiers as $quartier)
                                <option value="{{ $quartier->id }}" @selected(old('quartier_id') == $quartier->id)>
                                    {{ $quartier->nom }} — {{ $quartier->ville }}
                                </option>
                            @endforeach
                        </select>
                        @error('quartier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="radius">Rayon</label>
                        <select class="form-select @error('radius') is-invalid @enderror" id="radius" name="radius">
                            @foreach ([1000 => '1 000 m', 3000 => '3 000 m', 5000 => '5 000 m'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('radius', 3000) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('radius')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary mt-4">Rechercher avec Geoapify</button>
            </form>
        </div>
    </div>
@endsection
