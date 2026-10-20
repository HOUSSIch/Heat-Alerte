<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f76707">
    <title>@yield('title', 'HeatAlert')</title>
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="auth-page">
    <main class="auth-wrapper">
        <section class="auth-panel" aria-label="Authentification HeatAlert">
            <div class="auth-brand">
                <a href="{{ url('/') }}" class="auth-brand-link" aria-label="Accueil HeatAlert">
                    <img src="{{ asset('images/heatalert-logo-final.png') }}" class="auth-logo" alt="HeatAlert">
                </a>
                <p class="auth-brand-tagline">Prévenir les risques liés aux fortes chaleurs</p>
            </div>

            @yield('content')
        </section>
    </main>
    <script>document.querySelectorAll('form').forEach((form) => form.setAttribute('novalidate', 'novalidate'));</script>
    @stack('scripts')
</body>

</html>
