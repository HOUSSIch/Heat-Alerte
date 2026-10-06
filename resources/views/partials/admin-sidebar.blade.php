<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('images/heatalert-logo-transparent.png') }}" alt="HeatAlert" class="brand-image opacity-100 shadow-none" style="width: 34px; height: 34px; object-fit: contain; margin-left: 0; margin-top: 0;">
            <span class="brand-text lh-sm">
                <span class="d-block fw-semibold">HeatAlert</span>
                <small class="d-block text-secondary fw-normal">Admin Panel</small>
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Navigation administration">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                <li class="nav-header">DASHBOARD</li>
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'dashboard', 'class' => 'nav-icon'])
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">GESTION</li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'user', 'class' => 'nav-icon'])
                        <p>Utilisateurs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.quartiers.index') }}" class="nav-link {{ request()->routeIs('admin.quartiers.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'map-pin', 'class' => 'nav-icon'])
                        <p>Quartiers</p>
                    </a>
                </li>

                <li class="nav-header">SURVEILLANCE</li>
                <li class="nav-item">
                    <a href="{{ route('admin.alertes-meteo.index') }}" class="nav-link {{ request()->routeIs('admin.alertes-meteo.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'thermometer', 'class' => 'nav-icon'])
                        <p>Alertes météo</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.coupures.index') }}" class="nav-link {{ request()->routeIs('admin.coupures.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'bolt', 'class' => 'nav-icon'])
                        <p>Coupures électriques</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.signalements.index') }}" class="nav-link {{ request()->routeIs('admin.signalements.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'alert', 'class' => 'nav-icon'])
                        <p>Signalements</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.points-fraicheur.index') }}" class="nav-link {{ request()->routeIs('admin.points-fraicheur.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'map-pin', 'class' => 'nav-icon'])
                        <p>Points de fraîcheur</p>
                    </a>
                </li>

                <li class="nav-header">PRÉVENTION</li>
                <li class="nav-item">
                    <a href="{{ route('admin.equipements.index') }}" class="nav-link {{ request()->routeIs('admin.equipements.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'devices', 'class' => 'nav-icon'])
                        <p>Équipements</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.conseils.index') }}" class="nav-link {{ request()->routeIs('admin.conseils.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'bulb', 'class' => 'nav-icon'])
                        <p>Conseils</p>
                    </a>
                </li>

                <li class="nav-header">COMMUNAUTÉ</li>
                <li class="nav-item">
                    <a href="{{ route('admin.avis.index') }}" class="nav-link {{ request()->routeIs('admin.avis.*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'message', 'class' => 'nav-icon'])
                        <p>Avis</p>
                    </a>
                </li>

                <li class="nav-header">COMPTE</li>
                <li class="nav-item">
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                        @include('partials.tabler-icon', ['name' => 'user', 'class' => 'nav-icon'])
                        <p>Profil administrateur</p>
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start text-danger">
                            @include('partials.tabler-icon', ['name' => 'logout', 'class' => 'nav-icon'])
                            <p>Déconnexion</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
