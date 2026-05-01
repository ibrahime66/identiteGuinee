@extends('layouts.app')

@section('title', 'Changer le mot de passe - Espace Citoyen')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Changer le mot de passe</h1>
                <p class="mb-0">Mettez à jour votre mot de passe pour sécuriser votre compte</p>
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
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Changement de mot de passe</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('citizen.password.update') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel *</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe *</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            <small class="text-muted">Minimum 6 caractères</small>
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe *</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="6">
                            @error('password_confirmation')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Conseils de sécurité</h6>
                            <ul class="mb-0 small">
                                <li>Utilisez un mot de passe unique et difficile à deviner</li>
                                <li>Combinez des lettres, chiffres et symboles</li>
                                <li>Ne partagez jamais votre mot de passe</li>
                                <li>Changez votre mot de passe régulièrement</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('citizen.profile') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
