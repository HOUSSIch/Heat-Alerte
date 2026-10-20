@csrf
@if (isset($entretien))
    @method('PUT')
@endif

<div class="row g-4">
    <div class="col-md-6">
        <label class="profile-form-label" for="titre">Titre</label>
        <input class="form-control profile-form-control @error('titre') is-invalid @enderror" id="titre" name="titre"
            value="{{ old('titre', $entretien->titre ?? '') }}" required>
        @error('titre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="profile-form-label" for="type">Type d’entretien</label>
        <input class="form-control profile-form-control @error('type') is-invalid @enderror" id="type" name="type"
            value="{{ old('type', $entretien->type ?? '') }}" placeholder="Ex. Nettoyage, contrôle" required>
        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="profile-form-label" for="date_prevue">Date prévue</label>
        <input class="form-control profile-form-control @error('date_prevue') is-invalid @enderror" id="date_prevue"
            type="date" name="date_prevue" value="{{ old('date_prevue', isset($entretien) ? $entretien->date_prevue?->format('Y-m-d') : '') }}" required>
        @error('date_prevue') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="profile-form-label" for="rappel_avant_jours">Rappel avant (jours)</label>
        <input class="form-control profile-form-control @error('rappel_avant_jours') is-invalid @enderror" id="rappel_avant_jours"
            type="number" min="0" max="365" name="rappel_avant_jours" value="{{ old('rappel_avant_jours', $entretien->rappel_avant_jours ?? '') }}">
        @error('rappel_avant_jours') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12">
        <label class="profile-form-label" for="description">Description</label>
        <textarea class="form-control profile-form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $entretien->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-12">
        <label class="profile-form-label" for="notes">Notes</label>
        <textarea class="form-control profile-form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $entretien->notes ?? '') }}</textarea>
        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="profile-form-actions">
    <button class="btn-main" type="submit"><i class="bi bi-check-lg"></i> {{ $submitLabel }}</button>
    <a class="profile-secondary-action" href="{{ route('equipements.show', $equipement) }}">Annuler</a>
</div>
