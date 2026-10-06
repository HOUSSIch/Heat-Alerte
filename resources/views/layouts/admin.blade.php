<!doctype html>
<html lang="fr" data-bs-theme="light"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>@yield('title', 'Administration HeatAlert')</title>@vite('resources/js/admin.js')</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary"><div class="app-wrapper">
@include('partials.admin-navbar')
@include('partials.admin-sidebar')
<main class="app-main" id="main"><div class="app-content-header"><div class="container-fluid"><div class="row align-items-center"><div class="col-sm-6"><h3 class="mb-0">@yield('page-title', 'Administration')</h3></div><div class="col-sm-6"><ol class="breadcrumb float-sm-end mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Accueil</a></li>@yield('breadcrumb')</ol></div></div></div></div><div class="app-content"><div class="container-fluid">
@if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger"><strong>Veuillez corriger les erreurs indiquées.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')</div></div></main>
@include('partials.admin-footer')
</div></body></html>
