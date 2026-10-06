@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')

<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Connexion à votre espace</h2>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form action="{{ route('login') }}" method="POST" autocomplete="on">
            @csrf
            <div class="mb-3"><label class="form-label" for="email">Adresse e-mail</label><input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-2"><label class="form-label" for="password">Mot de passe</label><input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" required autocomplete="current-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-footer"><button class="btn btn-orange w-100" type="submit">Se connecter</button></div>
        </form>
    </div>
    <div class="hr-text">ou</div>
    <div class="card-body"><div class="text-center text-secondary">Pas encore de compte ? <a href="{{ route('register') }}" tabindex="-1">Créer un compte</a></div></div>
</div>

@endsection
