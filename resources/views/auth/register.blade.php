@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')

<div class="card card-md">
    <div class="card-body">
    <h2 class="h2 text-center mb-4">Créer votre compte habitant</h2>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert"><ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-3"><label class="form-label" for="name">Nom complet</label><input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

        <div class="mb-3"><label class="form-label" for="email">Adresse e-mail</label><input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

        <div class="mb-3"><label class="form-label" for="password">Mot de passe</label><input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" required autocomplete="new-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

        <div class="mb-3"><label class="form-label" for="password_confirmation">Confirmer le mot de passe</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div>

        <div class="form-footer"><button type="submit" class="btn btn-orange w-100">Créer mon compte</button></div>
    </form>

    </div>
    <div class="card-body"><div class="text-center text-secondary">Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></div></div>
</div>

@endsection
