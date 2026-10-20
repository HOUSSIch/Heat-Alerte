@php($editing = $pointFraicheur->exists)

<form method="POST"
    action="{{ $editing ? route('admin.points-fraicheur.update', $pointFraicheur) : route('admin.points-fraicheur.store') }}">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="quartier_id">Quartier</label>
                    <select class="form-select @error('quartier_id') is-invalid @enderror" id="quartier_id" name="quartier_id" required>
                        @foreach ($quartiers as $quartier)
                            <option value="{{ $quartier->id }}"
                                @selected(old('quartier_id', $pointFraicheur->quartier_id) == $quartier->id)>
                                {{ $quartier->nom }} — {{ $quartier->ville }}
                            </option>
                        @endforeach
                    </select>
                    @error('quartier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @foreach ([
                    ['nom', 'Nom', 'text', true, 6],
                    ['type', 'Type', 'text', true, 6],
                    ['adresse', 'Adresse', 'text', true, 6],
                    ['latitude', 'Latitude', 'number', true, 6],
                    ['longitude', 'Longitude', 'number', true, 6],
                    ['horaires', 'Horaires', 'text', false, 6],
                    ['telephone', 'Téléphone', 'text', false, 6],
                ] as [$field, $label, $type, $required, $column])
                    <div class="col-md-{{ $column }}">
                        <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                        <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}"
                            type="{{ $type }}" name="{{ $field }}"
                            @if ($type === 'number') step="any" @endif
                            value="{{ old($field, $pointFraicheur->{$field}) }}"
                            @if ($field === 'type') placeholder="Parc, centre communautaire…" @endif
                            @required($required)>
                        @error($field)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach

                <div class="col-12">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                        rows="4">{{ old('description', $pointFraicheur->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <div class="row g-3">
                        @foreach ([
                            ['climatise', 'Climatisé'],
                            ['eau_disponible', 'Eau disponible'],
                            ['accessible_pmr', 'Accessible PMR'],
                            ['actif', 'Actif'],
                        ] as [$field, $label])
                            <div class="col-md-3">
                                <label class="form-check form-switch" for="{{ $field }}">
                                    <input class="form-check-input" id="{{ $field }}" type="checkbox" name="{{ $field }}"
                                        value="1"
                                        @checked(old($field, $pointFraicheur->exists ? $pointFraicheur->{$field} : $field === 'actif'))>
                                    <span class="form-check-label">{{ $label }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a class="btn btn-outline-secondary"
                href="{{ $editing ? route('admin.points-fraicheur.show', $pointFraicheur) : route('admin.points-fraicheur.index') }}">
                Annuler
            </a>
            <button class="btn btn-primary">{{ $editing ? 'Enregistrer les modifications' : 'Créer le point' }}</button>
        </div>
    </div>
</form>
