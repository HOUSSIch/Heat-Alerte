<section class="contact section">
    <div class="container" data-aos="fade-up">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <form action="{{ $action }}" method="POST" class="equipment-form" enctype="multipart/form-data">
                    @csrf
                    @if ($method === 'PUT')
                        @method('PUT')
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Veuillez corriger les erreurs ci-dessous.</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h3>Informations de l’équipement</h3>
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <label for="nom">Nom</label>
                            <input id="nom" class="form-control @error('nom') is-invalid @enderror" name="nom" value="{{ old('nom', $equipement->nom ?? '') }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="type">Type</label>
                            <select id="type" class="form-select @error('type') is-invalid @enderror" name="type" required>
                                <option value="">Sélectionner un type</option>
                                @foreach (['Électroménager', 'Informatique', 'Climatisation', 'Médical', 'Télécommunication', 'Autre'] as $type)
                                    <option value="{{ $type }}" @selected(old('type', $equipement->type ?? '') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="marque">Marque <small>Optionnel</small></label>
                            <input id="marque" class="form-control @error('marque') is-invalid @enderror" name="marque" value="{{ old('marque', $equipement->marque ?? '') }}">
                            @error('marque')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="description">Description <small>Optionnel</small></label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="5" placeholder="Ex. appareil indispensable à conserver pendant une coupure.">{{ old('description', $equipement->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <div class="equipment-photo-field">
                                <label for="photo">{{ !empty($equipement?->photo) ? 'Remplacer la photo' : 'Photo de l’équipement' }} <small>Optionnel</small></label>
                                @if (!empty($equipement?->photo))
                                    <img class="equipment-photo-preview" src="{{ asset('storage/' . $equipement->photo) }}" alt="Photo actuelle de {{ $equipement->nom }}">
                                @endif
                                <input id="photo" class="form-control @error('photo') is-invalid @enderror" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
                                <small>Ajoutez une photo pour reconnaître facilement votre équipement.</small>
                                @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-12"><h4 class="mt-2">Sensibilités</h4></div>
                        <div class="col-md-6">
                            <label class="equipment-switch">
                                <input type="checkbox" name="sensible_chaleur" value="1" @checked(old('sensible_chaleur', $equipement->sensible_chaleur ?? false))>
                                <span><strong>Sensible à la chaleur</strong><small>Nécessite une attention pendant les fortes températures.</small></span>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="equipment-switch">
                                <input type="checkbox" name="sensible_coupure" value="1" @checked(old('sensible_coupure', $equipement->sensible_coupure ?? false))>
                                <span><strong>Sensible aux coupures</strong><small>Peut être affecté par une interruption électrique.</small></span>
                            </label>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-3 mt-3">
                            <a href="{{ route('equipements.index') }}" class="equipment-form-cancel"><i class="bi bi-arrow-left"></i> Annuler</a>
                            <button type="submit" class="btn-main"><i class="bi bi-check-lg"></i> {{ $submitLabel }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
