<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HeatAlert')</title>
    <meta name="description" content="HeatAlert : prévention locale face aux fortes chaleurs et aux coupures électriques.">
    <link href="{{ asset('front/workfolio/assets/img/favicon.png') }}" rel="icon">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="{{ asset('front/workfolio/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('front/workfolio/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('front/workfolio/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('front/workfolio/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('front/workfolio/assets/css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('front/workfolio/assets/css/heatalert.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="@yield('body-class', 'index-page')">
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
            <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center"><img class="heatalert-logo" src="{{ asset('images/heatalert-logo-final.png') }}" alt="Logo HeatAlert"><h1 class="sitename">HeatAlert</h1></a>
            <nav id="navmenu" class="navmenu"><ul>
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Accueil</a></li>
                <li><a href="{{ route('meteo.index') }}" class="{{ request()->routeIs('meteo.*') ? 'active' : '' }}">Météo</a></li>
                @auth<li><a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">Notifications @if(auth()->user()->unreadNotifications->count())<span class="badge text-bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>@endif</a></li>@endauth
                <li><a href="{{ route('coupures.index') }}" class="{{ request()->routeIs('coupures.*') ? 'active' : '' }}">Coupures</a></li>
                <li><a href="{{ route('points-fraicheur.index') }}" class="{{ request()->routeIs('points-fraicheur.*') ? 'active' : '' }}">Points de fraîcheur</a></li>
                <li class="dropdown"><a href="#"><span>Mon espace</span><i class="bi bi-chevron-down toggle-dropdown"></i></a><ul>
                    <li><a href="{{ route('discussions-quartier.index') }}">Discussions du quartier</a></li><li><a href="{{ route('etablissements-sensibles.index') }}">Structures d’assistance</a></li><li><a href="{{ route('equipements.index') }}">Mes équipements</a></li><li><a href="{{ route('conseils.mes') }}">Mes conseils</a></li><li><a href="{{ route('signalements.index') }}">Mes signalements</a></li><li><a href="{{ route('chatbot.index') }}">Assistant HeatAlert</a></li>
                </ul></li>
                @auth
                    <li class="dropdown"><a href="#"><span>Profil</span><i class="bi bi-chevron-down toggle-dropdown"></i></a><ul><li><a href="{{ route('profile.show') }}">Mon profil</a></li><li><form method="POST" action="{{ route('logout') }}">@csrf<button class="front-logout" type="submit">Déconnexion</button></form></li></ul></li>
                @else
                    <li><a href="{{ route('login') }}">Connexion</a></li><li><a href="{{ route('register') }}">Inscription</a></li>
                @endauth
            </ul><i class="mobile-nav-toggle d-xl-none bi bi-list"></i></nav>
            <a class="cta-btn d-none d-xl-inline-flex" href="{{ route('chatbot.index') }}">Besoin d’aide <i class="bi bi-arrow-up-right"></i></a>
        </div>
    </header>
    <main class="main">@yield('content')</main>
    <footer id="footer" class="footer"><div class="container footer-top"><div class="row gy-4"><div class="col-lg-4 col-md-12 footer-about"><a href="{{ route('dashboard') }}" class="logo d-flex align-items-center"><span class="sitename">HeatAlert</span></a><p>Des informations locales pour mieux se protéger pendant les fortes chaleurs et les coupures électriques.</p></div><div class="col-lg-2 col-6 footer-links"><h4>Navigation</h4><ul><li><a href="{{ route('dashboard') }}">Accueil</a></li><li><a href="{{ route('meteo.index') }}">Météo</a></li><li><a href="{{ route('coupures.index') }}">Coupures</a></li><li><a href="{{ route('points-fraicheur.index') }}">Points de fraîcheur</a></li></ul></div><div class="col-lg-3 col-6 footer-links"><h4>Services</h4><ul><li><a href="{{ route('etablissements-sensibles.index') }}">Structures</a></li><li><a href="{{ route('conseils.mes') }}">Conseils</a></li><li><a href="{{ route('chatbot.index') }}">Assistant</a></li></ul></div><div class="col-lg-3 col-md-12 footer-contact"><h4>Informations</h4><p>Prévention locale</p><p>Application universitaire HeatAlert</p></div></div></div><div class="container copyright text-center mt-4"><p>© <strong class="px-1 sitename">HeatAlert</strong> — Prévention et assistance locale</p><div class="credits">Template <a href="https://bootstrapmade.com/workfolio-bootstrap-portfolio-template/">Workfolio</a> par <a href="https://bootstrapmade.com/">BootstrapMade</a></div></div></footer>
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a><div id="preloader"></div>
    <script src="{{ asset('front/workfolio/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script><script src="{{ asset('front/workfolio/assets/vendor/aos/aos.js') }}"></script><script src="{{ asset('front/workfolio/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script><script src="{{ asset('front/workfolio/assets/vendor/swiper/swiper-bundle.min.js') }}"></script><script src="{{ asset('front/workfolio/assets/js/main.js') }}"></script>
    <script>document.querySelectorAll('form').forEach((form) => form.setAttribute('novalidate', 'novalidate'));</script>
    @stack('scripts')
</body>
</html>
