@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="d-flex"><div><strong>Veuillez corriger les erreurs ci-dessous.</strong></div></div>
        <ul class="mb-0 mt-2 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif
    <div class="card">
        <div class="card-header"><h3 class="card-title">Informations de l'équipement</h3></div>
        <div class="card-body">
            <div class="row row-cards">
                <div class="col-md-6">
                    <div class="mb-3"><label class="form-label" for="nom">Nom</label><input class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $equipement->nom ?? '') }}" required autofocus>@error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3"><label class="form-label" for="type">Type</label><select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required><option value="">Sélectionner un type</option>@foreach (['Électroménager', 'Informatique', 'Climatisation', 'Médical', 'Télécommunication', 'Autre'] as $type)<option value="{{ $type }}" @selected(old('type', $equipement->type ?? '') === $type)>{{ $type }}</option>@endforeach</select>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3"><label class="form-label" for="marque">Marque <span class="form-label-description">Optionnel</span></label><input class="form-control @error('marque') is-invalid @enderror" id="marque" name="marque" value="{{ old('marque', $equipement->marque ?? '') }}">@error('marque')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Description <span class="form-label-description">Optionnel</span></label><textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Ex. appareil indispensable à conserver pendant une coupure.">{{ old('description', $equipement->description ?? '') }}</textarea>@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12"><div class="hr-text">Sensibilités</div></div>
                <div class="col-md-6"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="sensible_chaleur" value="1" @checked(old('sensible_chaleur', $equipement->sensible_chaleur ?? false))><span class="form-check-label">Sensible à la chaleur</span><span class="form-check-description">Nécessite une attention pendant les fortes températures.</span></label></div>
                <div class="col-md-6"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="sensible_coupure" value="1" @checked(old('sensible_coupure', $equipement->sensible_coupure ?? false))><span class="form-check-label">Sensible aux coupures</span><span class="form-check-description">Peut être affecté par une interruption électrique.</span></label></div>
            </div>
        </div>
        <div class="card-footer text-end"><a href="{{ route('equipements.index') }}" class="btn btn-link">Annuler</a><button type="submit" class="btn btn-orange">{{ $submitLabel }}</button></div>
    </div>
</form>
