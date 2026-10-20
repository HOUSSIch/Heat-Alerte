@extends('layouts.auth')

@section('title', 'Confirmer le mot de passe')

@section('content')
    <section class="auth-card">
        <h1 class="auth-title">Confirmer votre mot de passe</h1>
        <p class="auth-subtitle">Cette zone est sécurisée. Saisissez votre mot de passe pour continuer.</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="auth-form-group">
                <label class="auth-label" for="password">Mot de passe</label>
                <input class="auth-input @error('password') is-invalid @enderror" id="password" type="password"
                    name="password" required autocomplete="current-password">
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <button class="auth-submit" type="submit">Confirmer</button>
        </form>
    </section>
@endsection
