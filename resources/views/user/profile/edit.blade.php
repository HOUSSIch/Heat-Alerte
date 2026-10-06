@extends('layouts.user')

@section('title', 'Modifier mon profil - HeatAlert')

@section('content')

<div class="page-header d-print-none mb-4">

    <div class="row align-items-center">

        <div class="col">

            <h2 class="page-title">
                Modifier mon profil
            </h2>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-md-8 col-lg-6">

        <div class="card">

            <div class="card-body">

                @if ($errors->any())

                    <div class="alert alert-danger">

                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach

                    </div>

                @endif


                <form
                    action="{{ route('profile.update') }}"
                    method="POST"
                >

                    @csrf
                    @method('PUT')


                    <div class="mb-3">

                        <label class="form-label">
                            Nom
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $user->name) }}"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $user->email) }}"
                            required
                        >

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quartier</label>
                        <select name="quartier_id" class="form-select">
                            <option value="">Choisir mon quartier</option>
                            @foreach($quartiers as $quartier)
                                <option value="{{ $quartier->id }}" @selected(old('quartier_id', $user->quartier_id) == $quartier->id)>{{ $quartier->nom }} — {{ $quartier->ville }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Nouveau mot de passe
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                        >

                        <small class="text-secondary">
                            Laissez vide si vous ne voulez pas le modifier.
                        </small>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Confirmer le mot de passe
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                        >

                    </div>


                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Enregistrer
                        </button>


                        <a
                            href="{{ route('profile.show') }}"
                            class="btn btn-secondary"
                        >
                            Annuler
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection
