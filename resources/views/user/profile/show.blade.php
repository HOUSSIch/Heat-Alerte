@extends('layouts.user')

@section('title', 'Mon profil - HeatAlert')

@section('content')

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">

        <div class="col">
            <div class="page-pretitle">Mon compte</div>
            <h2 class="page-title">Mon profil</h2>

            <div class="text-secondary mt-1">
                Gérez vos informations personnelles et votre quartier.
            </div>
        </div>

    </div>
</div>


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


<div class="row row-cards">
    <div class="col-md-8 col-lg-6">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Informations personnelles
                </h3>
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <span class="avatar avatar-xl rounded-circle bg-orange-lt text-orange fs-1 me-3">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <div>
                        <div class="h2 mb-1">{{ $user->name }}</div>
                        <div class="text-secondary">Compte HeatAlert</div>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Nom</strong>
                    <div class="text-secondary">
                        {{ $user->name }}
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Email</strong>
                    <div class="text-secondary">
                        {{ $user->email }}
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Rôle</strong>
                    <div>
                        <span class="badge bg-blue-lt">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Quartier</strong>
                    <div class="text-secondary">{{ $user->quartier ? $user->quartier->nom.' — '.$user->quartier->ville : 'Non renseigné' }}</div>
                </div>

                <div class="mb-3">
                    <strong>Membre depuis</strong>
                    <div class="text-secondary">
                        {{ $user->created_at->format('d/m/Y') }}
                    </div>
                </div>

            </div>


            <div class="card-footer d-flex justify-content-between">

                <a
                    href="{{ route('profile.edit') }}"
                    class="btn btn-primary"
                >
                    Modifier mon profil
                </a>


                <form
                    action="{{ route('profile.destroy') }}"
                    method="POST"
                    onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?')"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Supprimer mon compte
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>

@endsection
