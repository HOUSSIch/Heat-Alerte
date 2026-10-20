@extends('admin.layouts.gentelella')

@section('title', $coupure->exists ? 'Modifier une coupure' : 'Créer une coupure')

@section('content')
    <form method="POST"
        action="{{ $coupure->exists ? route('admin.coupures.update', $coupure) : route('admin.coupures.store') }}">
        @csrf
        @if ($coupure->exists)
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-body">
                <select class="form-select mb-2 @error('quartier_id') is-invalid @enderror" name="quartier_id" required>
                    <option value="">Sélectionnez un quartier</option>
                    @foreach ($quartiers as $quartier)
                        <option value="{{ $quartier->id }}" @selected(old('quartier_id', $coupure->quartier_id) == $quartier->id)>{{ $quartier->nom }}</option>
                    @endforeach
                </select>
                @error('quartier_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <input class="form-control mb-2" name="titre" value="{{ old('titre', $coupure->titre) }}"
                    placeholder="Titre" required>
                @error('titre')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <select class="form-select mb-2" name="type" required>
                    @foreach (['panne', 'maintenance', 'délestage'] as $type)
                        <option value="{{ $type }}" @selected(old('type', $coupure->type) === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                @error('type')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <select class="form-select mb-2" name="statut" required>
                    @foreach (['prévue', 'en_cours', 'terminée'] as $statut)
                        <option value="{{ $statut }}" @selected(old('statut', $coupure->statut) === $statut)>
                            {{ str_replace('_', ' ', ucfirst($statut)) }}</option>
                    @endforeach
                </select>
                @error('statut')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <textarea class="form-control mb-2" name="description" placeholder="Description">{{ old('description', $coupure->description) }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <div class="border-top mt-3 pt-3">
                    <h3 class="h6 mb-3">Planification</h3>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="date_debut">Date et heure de début</label>
                            <input class="form-control @error('date_debut') is-invalid @enderror" id="date_debut"
                                name="date_debut" type="datetime-local"
                                value="{{ old('date_debut', $coupure->date_debut?->format('Y-m-d\\TH:i')) }}">
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="date_fin_estimee">Fin estimée</label>
                            <input class="form-control @error('date_fin_estimee') is-invalid @enderror"
                                id="date_fin_estimee" name="date_fin_estimee" type="datetime-local"
                                value="{{ old('date_fin_estimee', $coupure->date_fin_estimee?->format('Y-m-d\\TH:i')) }}">
                            @error('date_fin_estimee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="date_fin_reelle">Fin réelle <span class="text-muted">(optionnelle)</span></label>
                            <input class="form-control @error('date_fin_reelle') is-invalid @enderror" id="date_fin_reelle"
                                name="date_fin_reelle" type="datetime-local"
                                value="{{ old('date_fin_reelle', $coupure->date_fin_reelle?->format('Y-m-d\\TH:i')) }}">
                            @error('date_fin_reelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <small class="text-muted d-block mb-3">L’adresse, la latitude et la longitude sont récupérées
                    automatiquement depuis le quartier sélectionné.</small>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1"
                        @checked(old('actif', $coupure->exists ? $coupure->actif : true))>
                    <label class="form-check-label" for="actif">Coupure active et visible pour les habitants du
                        quartier</label>
                </div>
            </div>
            <div class="card-footer"><button class="btn btn-primary">Enregistrer</button></div>
        </div>
    </form>
@endsection
