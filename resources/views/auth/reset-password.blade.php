@extends('layouts.auth')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
    <section class="auth-card">
        <h1 class="auth-title">Nouveau mot de passe</h1>
        <p class="auth-subtitle">Choisissez un mot de passe robuste pour sécuriser votre compte.</p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="auth-form-group">
                <label class="auth-label" for="email">Adresse e-mail</label>
                <input class="auth-input @error('email') is-invalid @enderror" id="email" type="email" name="email"
                    value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
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

            <button class="auth-submit" type="submit">Réinitialiser le mot de passe</button>
        </form>
    </section>
@endsection
