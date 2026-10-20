@extends('layouts.auth')

@section('title', 'Vérification en deux étapes')

@section('content')
    <section class="auth-card">
        <h1 class="auth-title">Vérification en deux étapes</h1>
        <p class="auth-subtitle">Entrez le code à 6 chiffres de votre application d’authentification.</p>

        <form method="POST" action="{{ route('two-factor.challenge.store') }}">
            @csrf
            <div class="auth-form-group">
                <label class="auth-label" for="code">Code</label>
                <input class="auth-input @error('code') is-invalid @enderror" id="code" name="code"
                    inputmode="numeric" autocomplete="one-time-code" maxlength="6">
                @error('code')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <p class="auth-subtitle">Ou utilisez un code de récupération :</p>
            <div class="auth-form-group">
                <label class="auth-label" for="recovery_code">Code de récupération</label>
                <input class="auth-input @error('recovery_code') is-invalid @enderror" id="recovery_code"
                    name="recovery_code" autocomplete="one-time-code">
                @error('recovery_code')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <button class="auth-submit" type="submit">Vérifier</button>
        </form>
    </section>
@endsection
