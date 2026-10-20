@extends('layouts.front')

@section('title', 'Modifier mon profil - HeatAlert')

@section('content')
    @include('partials.front-page-title', [
        'title' => 'Modifier mon profil',
        'subtitle' => 'Mettez à jour vos informations personnelles et votre quartier.',
    ])

    <section class="section profile-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9" data-aos="fade-up">
                    <article class="profile-edit-card">
                        <div class="profile-card-heading">
                            <div>
                                <p class="profile-kicker">Paramètres du compte</p>
                                <h2>Informations personnelles</h2>
                                <p class="profile-card-intro">Ces informations permettent à HeatAlert de vous proposer des données locales pertinentes.</p>
                            </div>
                            <i class="bi bi-sliders profile-heading-icon" aria-hidden="true"></i>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger profile-alert" role="alert">
                                <strong>Veuillez corriger les champs indiqués.</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="profile-update-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="profile-form-label" for="profile_photo">Photo de profil</label>
                                    <input class="form-control profile-form-control @error('profile_photo') is-invalid @enderror"
                                        id="profile_photo" type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <p class="profile-field-help">JPG, PNG ou WebP, 2 Mo maximum.</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="profile-form-label" for="name">Nom complet</label>
                                    <input class="form-control profile-form-control @error('name') is-invalid @enderror"
                                        id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                                        autocomplete="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="profile-form-label" for="email">Adresse e-mail</label>
                                    <input class="form-control profile-form-control @error('email') is-invalid @enderror"
                                        id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                                        autocomplete="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="profile-form-label" for="quartier_id">Mon quartier</label>
                                    <select class="form-select profile-form-control @error('quartier_id') is-invalid @enderror"
                                        id="quartier_id" name="quartier_id">
                                        <option value="">Choisir mon quartier</option>
                                        @foreach ($quartiers as $quartier)
                                            <option value="{{ $quartier->id }}"
                                                @selected(old('quartier_id', $user->quartier_id) == $quartier->id)>
                                                {{ $quartier->nom }} — {{ $quartier->ville }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('quartier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <p class="profile-field-help"><i class="bi bi-geo-alt"></i> Utilisé pour vos alertes et services locaux.</p>
                                </div>
                            </div>

                            <div class="profile-security-section">
                                <div class="profile-section-title">
                                    <i class="bi bi-shield-lock"></i>
                                    <div>
                                        <h3>Sécurité</h3>
                                        <p>Modifiez votre mot de passe uniquement si nécessaire.</p>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="profile-form-label" for="current_password">Mot de passe actuel</label>
                                        <input class="form-control profile-form-control @error('current_password') is-invalid @enderror"
                                            id="current_password" type="password" name="current_password" autocomplete="current-password">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <p class="profile-field-help">Obligatoire uniquement pour changer votre mot de passe.</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="profile-form-label" for="password">Nouveau mot de passe</label>
                                        <input class="form-control profile-form-control @error('password') is-invalid @enderror"
                                            id="password" type="password" name="password" autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="profile-form-label" for="password_confirmation">Confirmer le mot de passe</label>
                                        <input class="form-control profile-form-control @error('password_confirmation') is-invalid @enderror"
                                            id="password_confirmation" type="password" name="password_confirmation"
                                            autocomplete="new-password">
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <p class="profile-field-help mb-0">Laissez ces deux champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>
                            </div>
                        </form>

                        <div class="profile-security-section">
                            <div class="profile-section-title">
                                <i class="bi bi-shield-check"></i>
                                <div>
                                    <h3>Authentification à deux facteurs</h3>
                                    <p>
                                        Statut :
                                        <strong>{{ $user->hasTwoFactorEnabled() ? 'activée' : 'désactivée' }}</strong>
                                    </p>
                                </div>
                            </div>

                            @if ($user->hasTwoFactorEnabled())
                                <p class="profile-field-help">Conservez ces codes en lieu sûr. Chaque code ne peut être utilisé qu’une seule fois.</p>
                                <div class="mb-3">
                                    @foreach ($user->two_factor_recovery_codes ?? [] as $code)
                                        <code class="me-2">{{ $code }}</code>
                                    @endforeach
                                </div>
                                <div class="profile-two-factor-actions">
                                    <form action="{{ route('two-factor.recovery-codes') }}" method="POST" class="profile-inline-form">
                                        @csrf
                                        <input class="form-control profile-form-control" name="current_password" type="password" placeholder="Mot de passe actuel" required autocomplete="current-password">
                                        <button class="profile-secondary-button" type="submit">Régénérer les codes</button>
                                    </form>
                                    <form action="{{ route('two-factor.disable') }}" method="POST" class="profile-inline-form" onsubmit="return confirm('Désactiver l’authentification à deux facteurs ?')">
                                        @csrf
                                        @method('DELETE')
                                        <input class="form-control profile-form-control" name="current_password" type="password" placeholder="Mot de passe actuel" required autocomplete="current-password">
                                        <button class="profile-danger-button" type="submit">Désactiver la 2FA</button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('two-factor.enable') }}" method="POST">
                                    @csrf
                                    <label class="profile-form-label" for="two_factor_password">Mot de passe actuel</label>
                                    <input class="form-control profile-form-control @error('current_password', 'twoFactorEnable') is-invalid @enderror"
                                        id="two_factor_password" name="current_password" type="password" required autocomplete="current-password">
                                    @error('current_password', 'twoFactorEnable')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <button class="profile-secondary-button mt-3" type="submit">Activer la 2FA</button>
                                </form>
                            @endif
                        </div>

                        <div class="profile-form-actions">
                            <button class="profile-primary-button" form="profile-update-form" type="submit"><i class="bi bi-check-lg"></i> Enregistrer les modifications</button>
                            <a href="{{ route('profile.show') }}" class="profile-secondary-button">Annuler</a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
