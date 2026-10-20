@extends('admin.layouts.gentelella')

@section('title', 'Alerte météo')

@section('page-title', $alerte->exists ? 'Modifier alerte' : 'Créer alerte')

@section('content')
    <form method="POST"
        action="{{ $alerte->exists ? route('admin.alertes-meteo.update', $alerte) : route('admin.alertes-meteo.store') }}">
        @csrf
        @if ($alerte->exists)
            @method('PUT')
        @endif

        <div class="card card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="quartier_id">Quartier</label>
                        <select class="form-select @error('quartier_id') is-invalid @enderror" id="quartier_id" name="quartier_id">
                            @foreach ($quartiers as $q)
                                <option value="{{ $q->id }}" @selected(old('quartier_id', $alerte->quartier_id) == $q->id)>
                                    {{ $q->nom }} — {{ $q->ville }}
                                </option>
                            @endforeach
                        </select>
                        @error('quartier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="titre">Titre</label>
                        <input class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre"
                            value="{{ old('titre', $alerte->titre) }}" required>
                        @error('titre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="temperature">Température</label>
                        <input class="form-control @error('temperature') is-invalid @enderror" id="temperature"
                            name="temperature" type="number" step="any" value="{{ old('temperature', $alerte->temperature) }}">
                        @error('temperature')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="temperature_max">Maximum</label>
                        <input class="form-control @error('temperature_max') is-invalid @enderror" id="temperature_max"
                            name="temperature_max" type="number" step="any"
                            value="{{ old('temperature_max', $alerte->temperature_max) }}">
                        @error('temperature_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="temperature_min">Minimum</label>
                        <input class="form-control @error('temperature_min') is-invalid @enderror" id="temperature_min"
                            name="temperature_min" type="number" step="any"
                            value="{{ old('temperature_min', $alerte->temperature_min) }}">
                        @error('temperature_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="niveau">Niveau</label>
                        <select class="form-select @error('niveau') is-invalid @enderror" id="niveau" name="niveau">
                            @foreach (['Normal', 'Vigilance', 'Alerte', 'Danger'] as $n)
                                <option value="{{ $n }}" @selected(old('niveau', $alerte->niveau) == $n)>{{ $n }}</option>
                            @endforeach
                        </select>
                        @error('niveau')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $alerte->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" id="actif" type="checkbox" name="actif" value="1"
                                @checked(old('actif', $alerte->actif ?? true))>
                            <label class="form-check-label" for="actif">Alerte active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-default" href="{{ route('admin.alertes-meteo.index') }}">Annuler</a>
                <button class="btn btn-danger float-end">Enregistrer</button>
            </div>
        </div>
    </form>
@endsection
