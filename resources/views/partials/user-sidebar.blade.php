<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">

        {{-- LOGO --}}
        <h1 class="navbar-brand navbar-brand-autodark py-3">
            <a href="{{ route('dashboard') }}"
               class="d-flex align-items-center gap-2 text-decoration-none">

                <img
                    src="{{ asset('images/heatalert-logo-transparent.png') }}"
                    class="heatalert-brand-logo"
                    alt="HeatAlert"
                >

                <span class="text-white fw-bold">
                    HeatAlert
                </span>
            </a>
        </h1>


        {{-- MENU TOUJOURS VISIBLE --}}
        <div class="navbar-collapse">

            <ul class="navbar-nav pt-lg-3">

                {{-- Dashboard --}}
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link"
                       href="{{ route('dashboard') }}">

                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'dashboard'])
                        </span>

                        <span class="nav-link-title">
                            Dashboard
                        </span>
                    </a>
                </li>


                {{-- Alertes météo --}}
                <li class="nav-item {{ request()->routeIs('meteo.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('meteo.index') }}">

                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'sun'])
                        </span>

                        <span class="nav-link-title">
                            Alertes météo
                        </span>
                    </a>
                </li>


                {{-- Coupures --}}
                <li class="nav-item {{ request()->routeIs('coupures.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('coupures.index') }}">

                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'bolt'])
                        </span>

                        <span class="nav-link-title">
                            Coupures électriques
                        </span>
                    </a>
                </li>


                {{-- Mes signalements --}}
                <li class="nav-item {{ request()->routeIs('signalements.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('signalements.index') }}">
                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'alert'])
                        </span>
                        <span class="nav-link-title">Mes signalements</span>
                    </a>
                </li>


                {{-- Points fraîcheur --}}
                <li class="nav-item {{ request()->routeIs('points-fraicheur.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('points-fraicheur.index') }}">

                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'map-pin'])
                        </span>

                        <span class="nav-link-title">
                            Points de fraîcheur
                        </span>
                    </a>
                </li>


                {{-- Equipements --}}
                <li class="nav-item {{ request()->routeIs('equipements.*') ? 'active' : '' }}">

                    <a class="nav-link"
                       href="{{ route('equipements.index') }}">

                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'devices'])
                        </span>

                        <span class="nav-link-title">
                            Mes équipements
                        </span>
                    </a>

                </li>


                {{-- Conseils --}}
                <li class="nav-item {{ request()->routeIs('conseils.*') ? 'active' : '' }}">

                    <a class="nav-link"
                       href="{{ route('conseils.mes') }}">

                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'bulb'])
                        </span>

                        <span class="nav-link-title">
                            Mes conseils
                        </span>
                    </a>

                </li>


                {{-- Chatbot --}}
                <li class="nav-item {{ request()->routeIs('chatbot.*') ? 'active' : '' }}">

                    <a class="nav-link" href="{{ route('chatbot.index') }}">

                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'message'])
                        </span>

                        <span class="nav-link-title">
                            Assistant HeatAlert
                        </span>
                    </a>

                </li>


                {{-- Profil --}}
                <li class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">

                    <a class="nav-link"
                       href="{{ route('profile.show') }}">

                        <span class="nav-link-icon">
                            @include('partials.tabler-icon', ['name' => 'user'])
                        </span>

                        <span class="nav-link-title">
                            Profil
                        </span>
                    </a>

                </li>

            </ul>

        </div>

    </div>
</aside>
