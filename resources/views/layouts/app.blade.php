<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'HeatAlert')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <header>
        <h2>HeatAlert</h2>

        <nav>
            <a href="{{ route('dashboard') }}">Dashboard</a>

            @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Déconnexion</button>
                </form>
            @endauth
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

</body>
</html>