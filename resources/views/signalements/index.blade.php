@extends('layouts.front')

@section('title', 'Mes signalements')

@section('content')
    @include('partials.front-page-title', ['title' => 'Mes signalements', 'subtitle' => 'Suivez les interruptions que vous avez signalées.'])
    <section class="blog-posts section"><div class="container d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5"><div class="section-title text-start p-0 m-0"><h2>Historique</h2><p>Vos contributions à l’information locale.</p></div><a class="btn-get-started" href="{{ route('signalements.create') }}">Nouveau signalement</a></div><div class="container"><div class="row gy-4">
        @forelse($signalements as $s)
            <div class="col-lg-4 col-md-6" data-aos="fade-up"><article class="heatalert-card"><p class="post-category">{{ $s->quartier->nom }}</p><h2 class="title">{{ $s->titre }}</h2><span class="badge text-bg-warning">{{ $s->statut }}</span><p class="mt-3">{{ $s->description }}</p><div class="post-meta">{{ $s->date_signalement->format('d/m/Y H:i') }}</div><a class="readmore mt-3 d-inline-flex" href="{{ route('signalements.show', $s) }}"><span>Voir le signalement</span><i class="bi bi-arrow-right"></i></a></article></div>
        @empty
            <div class="col-12"><div class="heatalert-panel text-center"><i class="bi bi-exclamation-triangle heatalert-icon mb-3"></i><h3>Aucun signalement</h3><p>Vous n’avez pas encore signalé de coupure.</p><a class="btn-get-started" href="{{ route('signalements.create') }}">Signaler une coupure</a></div></div>
        @endforelse
    </div></div></section>
@endsection
