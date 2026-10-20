@extends('admin.layouts.gentelella')

@section('title', 'Quartier')

@section('page-title', $quartier->exists ? 'Modifier quartier' : 'Créer quartier')

@section('content')
    <form method="POST"
        action="{{ $quartier->exists ? route('admin.quartiers.update', $quartier) : route('admin.quartiers.store') }}">
        @csrf
        @if ($quartier->exists)
            @method('PUT')
        @endif

        <div class="card card">
            <div class="card-body">
                <div class="row g-3">
                    @foreach ([
                        ['nom', 'Nom', 'text', true],
                        ['ville', 'Ville', 'text', true],
                        ['code_postal', 'Code postal', 'text', false],
                        ['latitude', 'Latitude', 'number', true],
                        ['longitude', 'Longitude', 'number', true],
                    ] as [$field, $label, $type, $required])
                        <div class="col-md-{{ in_array($field, ['nom', 'ville'], true) ? '6' : '4' }}">
                            <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                            <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}"
                                name="{{ $field }}" type="{{ $type }}" @if ($type === 'number') step="any" @endif
                                value="{{ old($field, $quartier->{$field}) }}" @required($required)>
                            @error($field)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" id="actif" type="checkbox" name="actif" value="1"
                                @checked(old('actif', $quartier->actif ?? true))>
                            <label class="form-check-label" for="actif">Quartier actif</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-default" href="{{ route('admin.quartiers.index') }}">Annuler</a>
                <button class="btn btn-primary float-end">Enregistrer</button>
            </div>
        </div>
    </form>
@endsection
