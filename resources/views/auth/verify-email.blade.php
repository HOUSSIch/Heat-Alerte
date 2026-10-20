@extends('layouts.auth')

@section('title', 'Vérifier votre adresse e-mail')

@section('content')
    <section class="auth-card">
        <h1 class="auth-title">Vérifiez votre adresse e-mail</h1>
        <p class="auth-subtitle">
            Un lien de vérification a été envoyé à votre adresse e-mail. Cliquez dessus avant de continuer.
        </p>

        @if (session('status') === 'verification-link-sent')
            <div class="auth-alert auth-alert--success" role="alert">
                Un nouveau lien de vérification a été envoyé.
            </div>
        @endif

        @if (session('error'))
            <div class="auth-alert auth-alert--error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="auth-submit" type="submit">Renvoyer l’e-mail de vérification</button>
        </form>

        <form class="auth-footer" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="auth-link" type="submit">Se déconnecter</button>
        </form>
    </section>
@endsection
