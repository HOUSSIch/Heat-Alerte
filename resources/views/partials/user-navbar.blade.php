<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <div class="navbar-nav flex-row order-md-last ms-auto">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Menu du profil">
                    <span class="avatar avatar-sm rounded-circle bg-orange-lt text-orange">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-secondary text-capitalize">{{ auth()->user()->role }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <div class="dropdown-header">Connecté en tant que {{ auth()->user()->role }}</div>
                    <a class="dropdown-item" href="{{ route('profile.show') }}">@include('partials.tabler-icon', ['name' => 'user', 'class' => 'icon icon-2 me-2']) Profil</a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">@include('partials.tabler-icon', ['name' => 'logout', 'class' => 'icon icon-2 me-2']) Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
