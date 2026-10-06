<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f76707">
    <title>@yield('title', 'HeatAlert')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex flex-column">
    <main class="page page-center min-vh-100 heat-auth-page">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark text-decoration-none d-inline-block">
                    <img src="{{ asset('images/heatalert-logo-transparent.png') }}" class="heatalert-auth-logo" alt="HeatAlert">
                </a>
                <p class="text-secondary mt-2 mb-0">Prévenir les risques liés aux fortes chaleurs</p>
            </div>

            @yield('content')
        </div>
    </main>
</body>
</html>
