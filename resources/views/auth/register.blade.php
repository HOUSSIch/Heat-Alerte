@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
    <section class="auth-card">
        <h1 class="auth-title">Créer votre compte habitant</h1>
        <p class="auth-subtitle">Restez informé et protégé face aux risques de chaleur.</p>

        @if ($errors->any())
            <div class="auth-alert auth-alert--danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="auth-form-group">
                <label class="auth-label" for="name">Nom complet</label>
                <input class="auth-input @error('name') is-invalid @enderror" id="name" type="text" name="name"
                    value="{{ old('name') }}" required autocomplete="name">
                @error('name')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="email">Adresse e-mail</label>
                <input class="auth-input @error('email') is-invalid @enderror" id="email" type="email" name="email"
                    value="{{ old('email') }}" required autocomplete="username">
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="password">Mot de passe</label>
                <input class="auth-input @error('password') is-invalid @enderror" id="password" type="password"
                    name="password" required autocomplete="new-password">
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="password_confirmation">Confirmer le mot de passe</label>
                <input class="auth-input @error('password_confirmation') is-invalid @enderror" id="password_confirmation"
                    type="password" name="password_confirmation" required autocomplete="new-password">
                @error('password_confirmation')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <button class="auth-submit" type="submit">Créer mon compte</button>
        </form>

        <p class="auth-footer">Déjà inscrit ? <a class="auth-link" href="{{ route('login') }}">Se connecter</a></p>
    </section>
@endsection
