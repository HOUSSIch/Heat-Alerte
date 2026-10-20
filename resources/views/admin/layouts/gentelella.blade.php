<!DOCTYPE html>
<html lang="fr" data-sw="off">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration HeatAlert')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin/gentelella/assets/main-v4-DB_ReeJG.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/gentelella/heatalert.css') }}">
    @stack('styles')
</head>
<body>
    <a class="skip-link" href="#main-content">Aller au contenu principal</a>

    {{-- Structure issue directement de production/index.html de Gentelella v4. --}}
    <aside class="sidebar" aria-label="Navigation principale">
        <div class="sidebar-brand">
            <img src="{{ asset('images/heatalert-logo-final.png') }}" alt="HeatAlert" class="heatalert-brand-logo">
            <div class="brand-name">HeatAlert <small>Administration</small></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-group">
                <div class="nav-label">Général</div>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    @include('partials.tabler-icon', ['name' => 'dashboard', 'class' => 'icon'])
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            <div class="nav-group">
                <div class="nav-label">Gestion territoriale</div>
                <a class="nav-link {{ request()->routeIs('admin.quartiers.*') ? 'active' : '' }}" href="{{ route('admin.quartiers.index') }}">@include('partials.tabler-icon', ['name' => 'map-pin', 'class' => 'icon'])<span class="nav-text">Quartiers</span></a>
                <a class="nav-link {{ request()->routeIs('admin.etablissements-sensibles.*') ? 'active' : '' }}" href="{{ route('admin.etablissements-sensibles.index') }}">@include('partials.tabler-icon', ['name' => 'building', 'class' => 'icon'])<span class="nav-text">Structures d’assistance</span></a>
            </div>
            <div class="nav-group">
                <div class="nav-label">Surveillance</div>
                <a class="nav-link {{ request()->routeIs('admin.alertes-meteo.*') ? 'active' : '' }}" href="{{ route('admin.alertes-meteo.index') }}">@include('partials.tabler-icon', ['name' => 'thermometer', 'class' => 'icon'])<span class="nav-text">Alertes météo</span></a>
                <a class="nav-link {{ request()->routeIs('admin.vigilance-meteo.*') ? 'active' : '' }}" href="{{ route('admin.vigilance-meteo.index') }}">@include('partials.tabler-icon', ['name' => 'thermometer', 'class' => 'icon'])<span class="nav-text">Vigilance météo</span></a>
                <a class="nav-link {{ request()->routeIs('admin.coupures.*') ? 'active' : '' }}" href="{{ route('admin.coupures.index') }}">@include('partials.tabler-icon', ['name' => 'bolt', 'class' => 'icon'])<span class="nav-text">Coupures électriques</span></a>
                <a class="nav-link {{ request()->routeIs('admin.coupures.planning') ? 'active' : '' }}" href="{{ route('admin.coupures.planning') }}">@include('partials.tabler-icon', ['name' => 'calendar', 'class' => 'icon'])<span class="nav-text">Planning des coupures</span></a>
                <a class="nav-link {{ request()->routeIs('admin.signalements.*') ? 'active' : '' }}" href="{{ route('admin.signalements.index') }}">@include('partials.tabler-icon', ['name' => 'alert', 'class' => 'icon'])<span class="nav-text">Signalements</span></a>
            </div>
            <div class="nav-group">
                <div class="nav-label">Points & services</div>
                <a class="nav-link {{ request()->routeIs('admin.points-fraicheur.*') ? 'active' : '' }}" href="{{ route('admin.points-fraicheur.index') }}">@include('partials.tabler-icon', ['name' => 'map-pin', 'class' => 'icon'])<span class="nav-text">Points de fraîcheur</span></a>
                <a class="nav-link" href="{{ route('admin.points-fraicheur.couverture') }}">@include('partials.tabler-icon', ['name' => 'dashboard', 'class' => 'icon'])<span class="nav-text">Couverture des points</span></a>
                <a class="nav-link {{ request()->routeIs('admin.avis.*') ? 'active' : '' }}" href="{{ route('admin.avis.index') }}">@include('partials.tabler-icon', ['name' => 'message', 'class' => 'icon'])<span class="nav-text">Avis</span></a>
            </div>
            <div class="nav-group">
                <div class="nav-label">Utilisateurs</div>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">@include('partials.tabler-icon', ['name' => 'user', 'class' => 'icon'])<span class="nav-text">Utilisateurs</span></a>
            </div>
            <div class="nav-group">
                <div class="nav-label">Communauté</div>
                <a class="nav-link {{ request()->routeIs('admin.discussions-quartier.*') ? 'active' : '' }}" href="{{ route('admin.discussions-quartier.index') }}">@include('partials.tabler-icon', ['name' => 'message', 'class' => 'icon'])<span class="nav-text">Discussions des quartiers</span></a>
            </div>
            <div class="nav-group">
                <div class="nav-label">Suivi équipements</div>
                <a class="nav-link {{ request()->routeIs('admin.equipements.*') ? 'active' : '' }}" href="{{ route('admin.equipements.index') }}">@include('partials.tabler-icon', ['name' => 'devices', 'class' => 'icon'])<span class="nav-text">Suivi équipements</span></a>
                <a class="nav-link {{ request()->routeIs('admin.entretiens.*') ? 'active' : '' }}" href="{{ route('admin.entretiens.index') }}">@include('partials.tabler-icon', ['name' => 'alert', 'class' => 'icon'])<span class="nav-text">Entretiens</span></a>
                <a class="nav-link {{ request()->routeIs('admin.conseils.*') ? 'active' : '' }}" href="{{ route('admin.conseils.index') }}">@include('partials.tabler-icon', ['name' => 'bulb', 'class' => 'icon'])<span class="nav-text">Conseils</span></a>
                <a class="nav-link {{ request()->routeIs('admin.assistant.*') ? 'active' : '' }}" href="{{ route('admin.assistant.index') }}">@include('partials.tabler-icon', ['name' => 'message', 'class' => 'icon'])<span class="nav-text">Assistant HeatAlert</span></a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}<span class="online"></span></div>
                <div class="sidebar-user-info"><div class="name">{{ auth()->user()->name ?? 'Administrateur' }}</div><div class="role">Administrateur</div></div>
            </div>
        </div>
    </aside>

    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" type="button" aria-label="Ouvrir le menu" aria-controls="sidebar" aria-expanded="false">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <nav class="breadcrumb" aria-label="Fil d’Ariane"><a href="{{ route('admin.dashboard') }}">Administration</a><span class="sep">›</span><span class="current">@yield('page-title', 'Dashboard')</span></nav>
        </div>
        <div class="topbar-right">
            <a class="tb-btn" href="{{ route('admin.profile') }}">@include('partials.tabler-icon', ['name' => 'user', 'class' => 'icon'])<span>Profil</span></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="tb-btn" type="submit">@include('partials.tabler-icon', ['name' => 'logout', 'class' => 'icon'])<span>Déconnexion</span></button></form>
        </div>
    </header>

    <main class="main" id="main-content">
        <div class="page-wrapper">
            <div class="page-header">
                <div class="page-header-row"><div><div class="page-pretitle">HeatAlert</div><h1 class="page-title">@yield('page-title', 'Dashboard')</h1></div>@yield('page-actions')</div>
            </div>
            @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="alert alert-danger"><strong>Veuillez corriger les erreurs indiquées.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div>
        <footer class="footer"><span>HeatAlert Administration — basé sur <a href="https://github.com/ColorlibHQ/gentelella" target="_blank" rel="noopener">Gentelella</a></span><span>MIT</span></footer>
    </main>

    <script type="module" src="{{ asset('admin/gentelella/js/main-v4-CmWkxJuV.js') }}"></script>
    <script>document.querySelectorAll('form').forEach((form) => form.setAttribute('novalidate', 'novalidate'));</script>
    @stack('scripts')
</body>
</html>
