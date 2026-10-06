@php($editing = $pointFraicheur->exists)
<form method="POST" action="{{ $editing ? route('admin.points-fraicheur.update', $pointFraicheur) : route('admin.points-fraicheur.store') }}">
    @csrf
    @if ($editing) @method('PUT') @endif
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Quartier</label><select class="form-select" name="quartier_id" required>@foreach ($quartiers as $quartier)<option value="{{ $quartier->id }}" @selected(old('quartier_id', $pointFraicheur->quartier_id) == $quartier->id)>{{ $quartier->nom }} — {{ $quartier->ville }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Nom</label><input class="form-control" name="nom" value="{{ old('nom', $pointFraicheur->nom) }}" required></div>
                <div class="col-md-6"><label class="form-label">Type</label><input class="form-control" name="type" value="{{ old('type', $pointFraicheur->type) }}" placeholder="Parc, centre communautaire…" required></div>
                <div class="col-md-6"><label class="form-label">Adresse</label><input class="form-control" name="adresse" value="{{ old('adresse', $pointFraicheur->adresse) }}" required></div>
                <div class="col-md-6"><label class="form-label">Latitude</label><input class="form-control" type="number" step="any" name="latitude" value="{{ old('latitude', $pointFraicheur->latitude) }}" required></div>
                <div class="col-md-6"><label class="form-label">Longitude</label><input class="form-control" type="number" step="any" name="longitude" value="{{ old('longitude', $pointFraicheur->longitude) }}" required></div>
                <div class="col-md-6"><label class="form-label">Horaires</label><input class="form-control" name="horaires" value="{{ old('horaires', $pointFraicheur->horaires) }}"></div>
                <div class="col-md-6"><label class="form-label">Téléphone</label><input class="form-control" name="telephone" value="{{ old('telephone', $pointFraicheur->telephone) }}"></div>
                <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4">{{ old('description', $pointFraicheur->description) }}</textarea></div>
                <div class="col-12"><div class="row g-3"><div class="col-md-3"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="climatise" value="1" @checked(old('climatise', $pointFraicheur->climatise))><span class="form-check-label">Climatisé</span></label></div><div class="col-md-3"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="eau_disponible" value="1" @checked(old('eau_disponible', $pointFraicheur->eau_disponible))><span class="form-check-label">Eau disponible</span></label></div><div class="col-md-3"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="accessible_pmr" value="1" @checked(old('accessible_pmr', $pointFraicheur->accessible_pmr))><span class="form-check-label">Accessible PMR</span></label></div><div class="col-md-3"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="actif" value="1" @checked(old('actif', $pointFraicheur->exists ? $pointFraicheur->actif : true))><span class="form-check-label">Actif</span></label></div></div></div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between"><a class="btn btn-outline-secondary" href="{{ $editing ? route('admin.points-fraicheur.show', $pointFraicheur) : route('admin.points-fraicheur.index') }}">Annuler</a><button class="btn btn-primary">{{ $editing ? 'Enregistrer les modifications' : 'Créer le point' }}</button></div>
    </div>
</form>
