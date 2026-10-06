@extends('layouts.user')

@section('title', 'Dashboard - HeatAlert')

@section('content')

<div class="page-header d-print-none">
    <div class="row align-items-center">

        <div class="col">
            <h2 class="page-title">
                Bienvenue {{ auth()->user()->name }} 👋!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            </h2>

            <div class="text-secondary mt-1">
                Tableau de bord HeatAlert
            </div>
        </div>

    </div>
</div>


<div class="row row-cards mt-3">dddddddddddddddddddddd

    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">

                <div class="subheader">
                    Température
                </div>

                <div class="h1 mb-3">
                    🌡 39°C
                </div>

                <div class="text-danger">
                    Forte chaleur
                </div>

            </div>
        </div>
    </div>


    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">

                <div class="subheader">
                    Coupures
                </div>

                <div class="h1 mb-3">
                    ⚡ 2
                </div>

                <div class="text-secondary">
                    Coupures signalées
                </div>

            </div>
        </div>
    </div>


    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">

                <div class="subheader">
                    Mes équipements
                </div>

                <div class="h1 mb-3">
                    🖥 0
                </div>

                <div class="text-secondary">
                    Équipements enregistrés
                </div>

            </div>
        </div>
    </div>


    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">

                <div class="subheader">
                    Conseils
                </div>

                <div class="h1 mb-3">
                    💡 0
                </div>

                <div class="text-secondary">
                    Conseils disponibles
                </div>

            </div>
        </div>
    </div>

</div>

@endsection