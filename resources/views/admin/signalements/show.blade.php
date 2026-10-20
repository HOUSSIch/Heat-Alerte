@extends('admin.layouts.gentelella')

@section('title', 'Signalement')

@section('content')
    <div class="card">
        <div class="card-body">
            <h3>{{ $signalement->titre }}</h3>
            <p>{{ $signalement->description }}</p>

            <form method="POST" action="{{ route('admin.signalements.update', $signalement) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label" for="statut">Statut</label>
                    <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut">
                        @foreach (['en_attente', 'validé', 'rejeté', 'résolu'] as $statut)
                            <option value="{{ $statut }}" @selected(old('statut', $signalement->statut) === $statut)>
                                {{ $statut }}
                            </option>
                        @endforeach
                    </select>
                    @error('statut')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="coupure_electrique_id">Coupure associée</label>
                    <select class="form-select @error('coupure_electrique_id') is-invalid @enderror"
                        id="coupure_electrique_id" name="coupure_electrique_id">
                        <option value="">Aucune coupure</option>
                        @foreach ($coupures as $coupure)
                            <option value="{{ $coupure->id }}"
                                @selected(old('coupure_electrique_id', $signalement->coupure_electrique_id) == $coupure->id)>
                                {{ $coupure->titre }}
                            </option>
                        @endforeach
                    </select>
                    @error('coupure_electrique_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </div>
@endsection
