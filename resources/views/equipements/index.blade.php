@extends('layouts.front')

@section('title', 'Mes équipements - HeatAlert')

@section('content')
    @include('partials.front-page-title', ['title' => 'Mes équipements', 'subtitle' => 'Protégez les appareils importants de votre foyer.'])

    <section class="blog-posts section">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5" data-aos="fade-up">
            <div class="section-title text-start p-0 m-0">
                <h2>Mes appareils</h2>
                <p>Suivez leurs sensibilités aux risques.</p>
            </div>
            <a href="{{ route('equipements.create') }}" class="btn-main"><i class="bi bi-plus-lg"></i> Ajouter un équipement</a>
        </div>

        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="row gy-4">
                @forelse ($equipements as $equipement)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <article class="heatalert-card">
                            @if ($equipement->photo)
                                <img class="equipment-card-photo" src="{{ asset('storage/' . $equipement->photo) }}" alt="{{ $equipement->nom }}">
                            @else
                                <div class="d-flex align-items-start justify-content-between">
                                    <i class="bi bi-cpu heatalert-icon"></i>
                                    <span class="post-category">{{ $equipement->type }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-start gap-3 mt-3">
                                <h2 class="title m-0">{{ $equipement->nom }}</h2>
                                @if ($equipement->photo)
                                    <span class="post-category">{{ $equipement->type }}</span>
                                @endif
                            </div>
                            <p>{{ $equipement->marque ?: 'Marque non renseignée' }}</p>
                            <p>{{ $equipement->description ?: 'Aucune description.' }}</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge text-bg-{{ $equipement->sensible_chaleur ? 'warning' : 'secondary' }}">Chaleur : {{ $equipement->sensible_chaleur ? 'sensible' : 'non' }}</span>
                                <span class="badge text-bg-{{ $equipement->sensible_coupure ? 'danger' : 'secondary' }}">Coupure : {{ $equipement->sensible_coupure ? 'sensible' : 'non' }}</span>
                            </div>
                            <div class="equipment-actions mt-4">
                                <a class="equipment-action equipment-action-view" href="{{ route('equipements.show', $equipement) }}"><i class="bi bi-eye"></i><span>Voir la fiche</span></a>
                                <button class="equipment-action equipment-action-view" type="button" data-bs-toggle="modal" data-bs-target="#qr-modal-{{ $equipement->id }}"><i class="bi bi-qr-code"></i><span>QR Code</span></button>
                                <a class="equipment-action equipment-action-edit" href="{{ route('equipements.edit', $equipement) }}"><i class="bi bi-pencil"></i><span>Modifier</span></a>
                                <form action="{{ route('equipements.destroy', $equipement) }}" method="POST" onsubmit="return confirm('Supprimer cet équipement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="equipment-action equipment-action-delete" type="submit"><i class="bi bi-trash"></i><span>Supprimer</span></button>
                                </form>
                            </div>
                        </article>
                    @include('partials.equipement-qr-modal', ['equipement' => $equipement])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="heatalert-panel text-center">
                            <i class="bi bi-cpu heatalert-icon mb-3"></i>
                            <h3>Aucun équipement enregistré</h3>
                            <p>Ajoutez vos appareils importants pour anticiper les risques.</p>
                            <a href="{{ route('equipements.create') }}" class="btn-main">Ajouter mon premier équipement</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
