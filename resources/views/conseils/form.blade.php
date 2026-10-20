<form action="{{ $action }}" method="POST">
    @csrf @if ($method === 'PUT')
        @method('PUT')
    @endif
    <div class="card card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">Informations du conseil</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="titre">Titre</label><input id="titre" class="form-control" name="titre"
                        value="{{ old('titre', $conseil->titre ?? '') }}" required>
                    @error('titre')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3"><label class="form-label" for="categorie">Catégorie</label><select id="categorie" class="form-select"
                        name="categorie" required>
                        @foreach (['Chaleur', 'Coupure électrique', 'Hydratation', 'Protection appareil', 'Sécurité', 'Économie d’énergie', 'Autre'] as $v)
                            <option @selected(old('categorie', $conseil->categorie ?? '') === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('categorie')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3"><label class="form-label" for="niveau">Niveau</label><select id="niveau" class="form-select" name="niveau"
                        required>
                        @foreach (['Information', 'Important', 'Urgent'] as $v)
                            <option @selected(old('niveau', $conseil->niveau ?? '') === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('niveau')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12"><label class="form-label" for="description">Description</label>
                    <textarea id="description" class="form-control" name="description" rows="4" required>{{ old('description', $conseil->description ?? '') }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <div class="form-check form-switch"><input id="actif" class="form-check-input" type="checkbox" name="actif"
                            value="1" @checked(old('actif', $conseil->actif ?? true))><label class="form-check-label" for="actif">Conseil
                            actif</label></div>
                </div>
                <fieldset class="col-12"><legend class="form-label fw-semibold">Équipements associés</legend>
                    <div class="border rounded p-3">
                        <div class="row">
                            @foreach ($equipements as $e)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check"><input id="equipement-{{ $e->id }}" class="form-check-input" type="checkbox"
                                            name="equipements[]" value="{{ $e->id }}"
                                            @checked(in_array($e->id, old('equipements', isset($conseil) ? $conseil->equipements->pluck('id')->all() : [])))><label
                                            class="form-check-label" for="equipement-{{ $e->id }}">{{ $e->nom }} <small
                                                class="text-secondary">({{ $e->user->name ?? 'Utilisateur' }})</small></label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('equipements')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('equipements.*')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </fieldset>
            </div>
        </div>
        <div class="card-footer"><a class="btn btn-default" href="{{ route('conseils.index') }}">Annuler</a><button
                class="btn btn-success float-end">{{ $label }}</button></div>
    </div>
</form>
