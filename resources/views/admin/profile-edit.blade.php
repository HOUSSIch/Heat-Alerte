@extends('admin.layouts.gentelella')

@section('title', 'Modifier mon profil')

@section('page-title', 'Modifier mon profil')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.profile') }}">Profil</a></li>
    <li class="breadcrumb-item active">Modification</li>
@endsection

@section('content')
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">Informations personnelles</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nom</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email"
                                name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <hr>
                        <p class="text-secondary small">
                            Laissez les champs suivants vides pour conserver votre mot de passe actuel.
                        </p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password">Nouveau mot de passe</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password"
                                    type="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="password_confirmation">Confirmation</label>
                                <input class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation" type="password" name="password_confirmation">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-default" href="{{ route('admin.profile') }}">Annuler</a>
                        <button class="btn btn-primary float-end">Enregistrer</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
