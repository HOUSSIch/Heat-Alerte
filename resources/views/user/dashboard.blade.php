@extends('layouts.front')

@section('title', 'HeatAlert — Prévention locale')

@section('content')
    @php($temperatureLabel = $weather ? $weather['temperature'] . '°C' : '—')
    <section id="hero" class="hero section"><div class="container"><div class="row align-items-center justify-content-center"><div class="col-lg-5 text-center text-lg-start" data-aos="fade-up"><span class="status"><span class="ping"></span> Informations locales en direct</span><h1>Anticipez la <span class="hl">chaleur</span>. Restez informé, restez protégé.</h1><p class="lead">HeatAlert vous aide à suivre les risques dans {{ $quartier?->nom ?? 'votre quartier' }} : météo, coupures et services utiles.</p><div class="actions"><a href="{{ route('meteo.index') }}" class="btn-main">Voir la météo</a><a href="{{ route('points-fraicheur.index') }}" class="btn-line">Points de fraîcheur <i class="bi bi-arrow-right"></i></a></div></div><div class="col-lg-7 text-center" data-aos="zoom-in" data-aos-delay="150"><div class="hero-workfolio-photo hero-heatalert-illustration"><img src="{{ asset('images/heatalert-hero.png') }}" alt="Illustration de vigilance chaleur HeatAlert" class="img-fluid"></div></div></div></div></section>

    <section id="about" class="about section light-background"><div class="container"><div class="row g-4 align-items-center"><div class="col-lg-5" data-aos="fade-up"><h2>Votre situation locale</h2><p>Des repères simples pour agir au bon moment et protéger les personnes comme les équipements.</p></div><div class="col-lg-7"><div class="row g-3 text-center"><div class="col-6 col-md-3"><div class="counter"><span data-purecounter-start="0" data-purecounter-end="{{ $weather ? $weather['temperature'] : 0 }}" data-purecounter-duration="1" class="purecounter"></span><p>Température actuelle</p></div></div><div class="col-6 col-md-3"><div class="counter"><span data-purecounter-start="0" data-purecounter-end="{{ $alertesActives->count() }}" data-purecounter-duration="1" class="purecounter"></span><p>alertes actives</p></div></div><div class="col-6 col-md-3"><div class="counter"><span data-purecounter-start="0" data-purecounter-end="{{ $coupuresActives }}" data-purecounter-duration="1" class="purecounter"></span><p>coupures actives</p></div></div><div class="col-6 col-md-3"><div class="counter"><span data-purecounter-start="0" data-purecounter-end="{{ $equipements->count() }}" data-purecounter-duration="1" class="purecounter"></span><p>{{ $equipements->count() }} équipement{{ $equipements->count() > 1 ? 's' : '' }} suivi{{ $equipements->count() > 1 ? 's' : '' }}</p></div></div></div></div></div></div></section>

    @if ($rappelsEntretien->isNotEmpty())
        <section class="section maintenance-dashboard-section">
            <div class="container" data-aos="fade-up">
                <div class="maintenance-dashboard-heading">
                    <div>
                        <p class="profile-kicker">Suivi de vos équipements</p>
                        <h2>Rappels d’entretien</h2>
                    </div>
                    <a class="btn-line" href="{{ route('equipements.index') }}">Mes équipements <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="row g-3">
                    @foreach ($rappelsEntretien as $rappel)
                        <div class="col-lg-4">
                            <a class="dashboard-maintenance-reminder dashboard-maintenance-reminder--{{ $rappel['entretien']->reminderStatus() }}" href="{{ route('equipements.show', $rappel['equipement']) }}">
                                <i class="bi {{ $rappel['entretien']->reminderStatus() === 'overdue' ? 'bi-exclamation-triangle-fill' : 'bi-bell-fill' }}"></i>
                                <div>
                                    <span>{{ $rappel['entretien']->reminderTitle() }}</span>
                                    <h3>{{ $rappel['equipement']->nom }}</h3>
                                    <p>{{ $rappel['entretien']->titre }} · {{ $rappel['entretien']->reminderDescription() }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="services" class="services section"><div class="container section-title" data-aos="fade-up"><h2>Les services HeatAlert</h2><p>Les informations essentielles, réunies au même endroit.</p></div><div class="container"><div class="row g-0 service-rows">
        @foreach ([['01','Météo et alertes','Suivez les températures et les bulletins de votre quartier.',route('meteo.index')],['02','Coupures électriques','Consultez et signalez les interruptions locales.',route('coupures.index')],['03','Points de fraîcheur','Repérez les lieux pour vous rafraîchir.',route('points-fraicheur.index')],['04','Structures d’assistance','Trouvez les contacts et services de proximité.',route('etablissements-sensibles.index')],['05','Assistant HeatAlert','Obtenez une réponse pratique immédiatement.',route('chatbot.index')]] as [$number,$title,$description,$url])
            <div class="col-12" data-aos="fade-up"><a href="{{ $url }}" class="row-item"><span class="n">{{ $number }}</span><div class="body"><h3>{{ $title }}</h3><p>{{ $description }}</p></div><i class="bi bi-arrow-up-right"></i></a></div>
        @endforeach
    </div></div></section>

    <section id="process" class="process section light-background"><div class="container section-title" data-aos="fade-up"><h2>Que faire en cas de forte chaleur ?</h2><p>Une démarche simple, inspirée de la section Process Workfolio.</p></div><div class="container"><div class="row g-4 steps"><div class="col-lg-3 col-md-6" data-aos="fade-up"><div class="step"><span class="num">01</span><i class="bi bi-cloud-sun"></i><h3>Consultez l’alerte</h3><p>Vérifiez la météo et le niveau de vigilance.</p></div></div><div class="col-lg-3 col-md-6" data-aos="fade-up"><div class="step"><span class="num">02</span><i class="bi bi-cup-straw"></i><h3>Hydratez-vous</h3><p>Adoptez les bons gestes de prévention.</p></div></div><div class="col-lg-3 col-md-6" data-aos="fade-up"><div class="step"><span class="num">03</span><i class="bi bi-geo-alt"></i><h3>Localisez un lieu frais</h3><p>Trouvez un point de fraîcheur à proximité.</p></div></div><div class="col-lg-3 col-md-6" data-aos="fade-up"><div class="step"><span class="num">04</span><i class="bi bi-chat-dots"></i><h3>Demandez de l’aide</h3><p>L’assistant vous oriente si nécessaire.</p></div></div></div></div></section>

    <section id="cta" class="cta section dark-background"><div class="container" data-aos="zoom-in"><div class="row justify-content-center text-center"><div class="col-lg-8"><div class="cta-center"><span class="kicker">HeatAlert vous accompagne</span><h2>Besoin d’aide ?</h2><p>Posez votre question à Assistant HeatAlert pour recevoir une réponse adaptée.</p><a href="{{ route('chatbot.index') }}" class="btn-main">Ouvrir l’assistant <i class="bi bi-arrow-right"></i></a></div></div></div></div></section>
@endsection
