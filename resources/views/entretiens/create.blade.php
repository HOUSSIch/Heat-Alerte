@extends('layouts.front')

@section('title', 'Ajouter un entretien - HeatAlert')

@section('content')
    @include('partials.front-page-title', ['title' => 'Ajouter un entretien', 'subtitle' => $equipement->nom . ' · Suivi et rappels'])
    <section class="section profile-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9">
                    <article class="profile-edit-card">
                        <div class="profile-card-heading">
                            <div>
                                <p class="profile-kicker">Entretien et suivi</p>
                                <h2>Planifier un entretien</h2>
                            </div>
                            <i class="bi bi-tools profile-heading-icon"></i>
                        </div>
                        <form action="{{ route('equipements.entretiens.store', $equipement) }}" method="POST">
                            @include('entretiens.form', ['submitLabel' => 'Ajouter l’entretien'])
                        </form>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
