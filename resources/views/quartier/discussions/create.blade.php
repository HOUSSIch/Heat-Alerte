@extends('layouts.front')

@section('title', 'Nouvelle discussion - HeatAlert')

@section('content')
    @include('partials.front-page-title', ['title' => 'Nouvelle discussion', 'subtitle' => 'Partagez une information avec les habitants de '.$quartier->nom.'.'])

    <section class="section">
        <div class="container"><div class="row justify-content-center"><div class="col-lg-8">
            <div class="heatalert-panel">
                <form method="POST" action="{{ route('discussions-quartier.store') }}">
                    @csrf
                    <div class="mb-3"><label class="form-label" for="quartier">Quartier</label><input id="quartier" class="form-control" value="{{ $quartier->nom }} — {{ $quartier->ville }}" readonly></div>
                    <div class="mb-3"><label class="form-label" for="titre">Titre</label><input id="titre" class="form-control" name="titre" value="{{ old('titre') }}" maxlength="255" required>@error('titre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
                    <div class="mb-3"><label class="form-label" for="type">Type</label><select id="type" class="form-select" name="type" required>@foreach (['probleme' => 'Problème', 'information' => 'Information', 'entraide' => 'Entraide', 'alerte' => 'Alerte'] as $value => $label)<option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>@endforeach</select>@error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
                    <div class="mb-4"><label class="form-label" for="contenu">Description</label><textarea id="contenu" class="form-control" name="contenu" rows="7" maxlength="5000" required>{{ old('contenu') }}</textarea>@error('contenu')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
                    <div class="d-flex gap-3"><button class="discussion-action" type="submit"><i class="bi bi-send"></i> Publier</button><a class="discussion-action discussion-action--secondary" href="{{ route('discussions-quartier.index') }}">Annuler</a></div>
                </form>
            </div>
        </div></div></div>
    </section>
@endsection
