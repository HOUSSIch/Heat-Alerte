@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
    <section class="auth-card">
        <h1 class="auth-title">Mot de passe oublié ?</h1>
        <p class="auth-subtitle">Saisissez votre adresse e-mail pour recevoir un lien de réinitialisation.</p>

        @if (session('status'))
            <div class="auth-alert auth-alert--success" role="alert">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="auth-alert auth-alert--error" role="alert">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="auth-form-group">
                <label class="auth-label" for="email">Adresse e-mail</label>
                <input class="auth-input @error('email') is-invalid @enderror" id="email" type="email" name="email"
                    value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <button class="auth-submit" type="submit">Envoyer le lien</button>
        </form>

        <p class="auth-footer"><a class="auth-link" href="{{ route('login') }}">Retour à la connexion</a></p>
    </section>
@endsection
