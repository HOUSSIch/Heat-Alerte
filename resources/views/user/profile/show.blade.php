@extends('layouts.front')

@section('title', 'Mon profil - HeatAlert')

@section('content')
    @include('partials.front-page-title', [
        'title' => 'Mon profil',
        'subtitle' => 'Gérez vos informations et personnalisez votre expérience HeatAlert.',
    ])

    <section class="section profile-page">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success profile-alert" role="alert">{{ session('success') }}</div>
            @endif

            <div class="row g-4 align-items-stretch">
                <div class="col-lg-4" data-aos="fade-up">
                    <aside class="profile-identity-card h-100">
                        @if ($user->profile_photo_path)
                            <img class="profile-avatar" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Photo de profil de {{ $user->name }}">
                        @else
                            <div class="profile-avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        @endif
                        <p class="profile-kicker">Espace personnel</p>
                        <h2>{{ $user->name }}</h2>
                        <p class="profile-email">{{ $user->email }}</p>
                        <div class="d-flex justify-content-center flex-wrap gap-2 mt-3">
                            <span class="profile-role-badge">{{ ucfirst($user->role) }}</span>
                            <span class="profile-status-badge"><i class="bi bi-check-circle-fill"></i> Compte actif</span>
                        </div>
                        <p class="profile-member-since">
                            <i class="bi bi-calendar3"></i>
                            Membre depuis {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                        </p>
                    </aside>
                </div>

                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                    <article class="profile-details-card h-100">
                        <div class="profile-card-heading">
                            <div>
                                <p class="profile-kicker">Vos informations</p>
                                <h2>Informations personnelles</h2>
                            </div>
                            <i class="bi bi-person-vcard profile-heading-icon" aria-hidden="true"></i>
                        </div>

                        <dl class="profile-details-list">
                            <div>
                                <dt>Nom</dt>
                                <dd>{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt>Adresse e-mail</dt>
                                <dd>{{ $user->email }}</dd>
                            </div>
                            <div class="profile-details-wide">
                                <dt>Quartier</dt>
                                <dd>
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $user->quartier ? $user->quartier->nom . ' — ' . $user->quartier->ville : 'Non renseigné' }}
                                </dd>
                            </div>
                        </dl>

                        <div class="profile-local-tip">
                            <i class="bi bi-lightbulb"></i>
                            <p>Renseigner votre quartier permet de personnaliser la météo, les coupures et les points de fraîcheur.</p>
                        </div>

                        <div class="profile-actions">
                            <a href="{{ route('profile.edit') }}" class="btn-main">
                                <i class="bi bi-pencil-square"></i> Modifier mon profil
                            </a>
                            <a href="{{ route('dashboard') }}" class="profile-secondary-action">
                                Retour à mon espace <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        <div class="profile-local-tip mt-4">
                            <i class="bi bi-shield-lock"></i>
                            <p>
                                Authentification à deux facteurs :
                                <strong>{{ $user->hasTwoFactorEnabled() ? 'activée' : 'désactivée' }}</strong>.
                                <a href="{{ route('profile.edit') }}">Gérer la sécurité du compte</a>
                            </p>
                        </div>

                        <div class="profile-danger-zone">
                            <div>
                                <h3>Supprimer mon compte</h3>
                                <p>Cette action est définitive et supprimera vos informations personnelles.</p>
                            </div>
                            <form action="{{ route('profile.destroy') }}" method="POST"
                                onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?')">
                                @csrf
                                @method('DELETE')
                                <label class="visually-hidden" for="delete-password">Mot de passe actuel</label>
                                <input class="form-control mb-2" id="delete-password" name="password" type="password" placeholder="Mot de passe actuel" required autocomplete="current-password">
                                <button type="submit" class="profile-danger-action">Supprimer mon compte</button>
                            </form>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
