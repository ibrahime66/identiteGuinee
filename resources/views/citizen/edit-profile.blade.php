@extends('layouts.app')

@section('title', 'Modifier mon profil - Espace Citoyen')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Modifier mon profil</h1>
                <p class="mb-0">Mettez à jour vos informations personnelles</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('citizen.profile') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Retour au profil
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('citizen.profile.update') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom complet *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $citizen->name }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="{{ $citizen->email }}" readonly>
                                    <small class="text-muted">L'email ne peut pas être modifié</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ $citizen->phone ?? '' }}">
                                    @error('phone')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cni_number" class="form-label">Numéro CNI</label>
                                    <input type="text" class="form-control" id="cni_number" name="cni_number" value="{{ $citizen->cni_number ?? '' }}">
                                    @error('cni_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Date de naissance</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ $citizen->birth_date ? $citizen->birth_date->format('Y-m-d') : '' }}">
                                    @error('birth_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_place" class="form-label">Lieu de naissance</label>
                                    <input type="text" class="form-control" id="birth_place" name="birth_place" value="{{ $citizen->birth_place ?? '' }}">
                                    @error('birth_place')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ $citizen->address ?? '' }}</textarea>
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profession" class="form-label">Profession</label>
                                    <input type="text" class="form-control" id="profession" name="profession" value="{{ $citizen->profession ?? '' }}">
                                    @error('profession')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nationality" class="form-label">Nationalité *</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" value="{{ $citizen->nationality }}" required>
                                    @error('nationality')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('citizen.profile') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
