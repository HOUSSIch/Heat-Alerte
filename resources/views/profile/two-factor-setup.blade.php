@extends('layouts.front')

@section('title', 'Authentification à deux facteurs - HeatAlert')

@section('content')
    @include('partials.front-page-title', [
        'title' => 'Vérification en deux étapes',
        'subtitle' => 'Associez une application TOTP compatible à votre compte.',
    ])

    <section class="section profile-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <article class="profile-edit-card">
                        <h2>Configurer votre application d’authentification</h2>
                        <p>Scannez ce QR code avec Google Authenticator, Microsoft Authenticator, Authy ou une application TOTP compatible.</p>
                        <div class="text-center my-4">{!! $qrCode !!}</div>
                        <p class="profile-field-help">Clé manuelle : <code>{{ $secret }}</code></p>

                        <form action="{{ route('two-factor.confirm') }}" method="POST">
                            @csrf
                            <label class="profile-form-label" for="code">Code à 6 chiffres</label>
                            <input class="form-control profile-form-control @error('code', 'twoFactorConfirm') is-invalid @enderror"
                                id="code" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required>
                            @error('code', 'twoFactorConfirm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <button class="btn-main mt-3" type="submit">Confirmer la 2FA</button>
                        </form>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
