<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'HeatAlert')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<div class="page">

    @include('partials.user-sidebar')


    {{-- CONTENU PRINCIPAL --}}
    <div class="page-wrapper">

        @include('partials.user-navbar')


        {{-- CONTENU DES PAGES --}}
        <div class="page-body">

            <div class="container-xl">

                @yield('content')

            </div>

        </div>


        @include('partials.user-footer')

    </div>

</div>

</body>

</html>
