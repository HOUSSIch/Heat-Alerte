@php($editing = $etablissementSensible->exists)

<form method="POST"
    action="{{ $editing ? route('admin.etablissements-sensibles.update', $etablissementSensible) : route('admin.etablissements-sensibles.store') }}">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="card card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="nom">Nom</label>
                    <input class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom"
                        value="{{ old('nom', $etablissementSensible->nom ?? '') }}" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="type">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">Sélectionnez un type</option>
                        @foreach (['Hôpital', 'Centre de santé', 'Maison de retraite', 'Crèche', 'École', 'Centre pour personnes fragiles', 'Autre'] as $type)
                            <option value="{{ $type }}" @selected(old('type', $etablissementSensible->type ?? '') === $type)>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="quartier_id">Quartier</label>
                    <select class="form-select @error('quartier_id') is-invalid @enderror" id="quartier_id"
                        name="quartier_id" required>
                        <option value="">Sélectionnez un quartier</option>
                        @foreach ($quartiers as $quartier)
                            <option value="{{ $quartier->id }}"
                                @selected(old('quartier_id', $etablissementSensible->quartier_id ?? '') == $quartier->id)>
                                {{ $quartier->nom }} — {{ $quartier->ville }}
                            </option>
                        @endforeach
                    </select>
                    @error('quartier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="telephone">Téléphone <span class="text-secondary">(facultatif)</span></label>
                    <input class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone"
                        value="{{ old('telephone', $etablissementSensible->telephone ?? '') }}" maxlength="30">
                    @error('telephone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="adresse">Adresse</label>
                    <input class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse"
                        value="{{ old('adresse', $etablissementSensible->adresse ?? '') }}" required>
                    @error('adresse')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Description <span class="text-secondary">(facultatif)</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                        name="description" rows="4">{{ old('description', $etablissementSensible->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="horaires">Horaires <span class="text-secondary">(facultatif)</span></label>
                    <input class="form-control @error('horaires') is-invalid @enderror" id="horaires" name="horaires"
                        value="{{ old('horaires', $etablissementSensible->horaires ?? '') }}" maxlength="255"
                        placeholder="Ex. Lun–Ven : 08:00–17:00">
                    @error('horaires')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="services">Services proposés <span class="text-secondary">(facultatif)</span></label>
                    <textarea class="form-control @error('services') is-invalid @enderror" id="services" name="services"
                        rows="3">{{ old('services', $etablissementSensible->services ?? '') }}</textarea>
                    @error('services')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" id="accueil_public" type="checkbox" name="accueil_public" value="1"
                            @checked(old('accueil_public', $etablissementSensible->accueil_public ?? false))>
                        <label class="form-check-label" for="accueil_public">Accueil du public</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" id="actif" type="checkbox" name="actif" value="1"
                            @checked(old('actif', $etablissementSensible->exists ? $etablissementSensible->actif : true))>
                        <label class="form-check-label" for="actif">Établissement actif et visible par les habitants</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a class="btn btn-default" href="{{ route('admin.etablissements-sensibles.index') }}">Annuler</a>
            <button class="btn btn-primary float-end">{{ $editing ? 'Enregistrer les modifications' : 'Créer l’établissement' }}</button>
        </div>
    </div>
</form>
