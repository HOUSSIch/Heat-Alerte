@extends('layouts.front')

@section('title', 'Modifier un entretien - HeatAlert')

@section('content')
    @include('partials.front-page-title', ['title' => 'Modifier un entretien', 'subtitle' => $equipement->nom . ' · ' . $entretien->titre])
    <section class="section profile-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9">
                    <article class="profile-edit-card">
                        <div class="profile-card-heading">
                            <div>
                                <p class="profile-kicker">Entretien et suivi</p>
                                <h2>Mettre à jour l’entretien</h2>
                            </div>
                            <i class="bi bi-tools profile-heading-icon"></i>
                        </div>
                        <form action="{{ route('equipements.entretiens.update', [$equipement, $entretien]) }}" method="POST">
                            @include('entretiens.form', ['submitLabel' => 'Enregistrer les modifications'])
                        </form>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
