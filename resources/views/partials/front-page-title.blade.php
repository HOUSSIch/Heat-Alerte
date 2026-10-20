<div class="page-title" data-aos="fade">
    <div class="heading">
        <div class="container"><div class="row d-flex justify-content-center text-center"><div class="col-lg-8">
            <h1>{{ $title }}</h1>
            @isset($subtitle)<p class="mb-0">{{ $subtitle }}</p>@endisset
        </div></div></div>
    </div>
    <nav class="breadcrumbs" aria-label="Fil d’Ariane"><div class="container"><ol><li><a href="{{ route('dashboard') }}">Accueil</a></li><li class="current">{{ $title }}</li></ol></div></nav>
</div>
