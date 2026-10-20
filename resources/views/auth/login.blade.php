@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
    <section class="auth-card">
        <h1 class="auth-title">Connexion à votre espace</h1>
        <p class="auth-subtitle">Accédez à vos alertes et informations HeatAlert.</p>

        @if (session('status'))
            <div class="auth-alert auth-alert--success" role="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="auth-alert auth-alert--danger" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" autocomplete="on">
            @csrf
            <div class="auth-form-group">
                <label class="auth-label" for="email">Adresse e-mail</label>
                <input class="auth-input @error('email') is-invalid @enderror" id="email" type="email" name="email"
                    value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="password">Mot de passe</label>
                <input class="auth-input @error('password') is-invalid @enderror" id="password" type="password"
                    name="password" required autocomplete="current-password">
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-actions">
                <label class="auth-check" for="remember">
                    <input id="remember" type="checkbox" name="remember" @checked(old('remember'))>
                    <span>Se souvenir de moi</span>
                </label>
                <a class="auth-link" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            </div>

            <button class="auth-submit" type="submit">Se connecter</button>
        </form>

        <p class="auth-footer">Pas encore de compte ? <a class="auth-link" href="{{ route('register') }}">Créer un compte</a></p>
    </section>
@endsection
