@extends('layouts.app')

@section('title', 'Connexion - Identiguinée')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-primary text-white text-center py-3">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-shield-alt fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">Identiguinée</h4>
                            <small class="opacity-75">Plateforme de gestion d'identité</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-sign-in-alt text-primary" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">Connexion</h4>
                        <p class="text-muted">Accédez à votre espace personnel</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required
                                       placeholder="Entrez votre email">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Mot de passe
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" required
                                       placeholder="Entrez votre mot de passe">
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-2">
                            <small class="text-muted">Pas encore de compte ?</small><br>
                            <a href="{{ route('citizen.register') }}" class="text-primary">
                                <i class="fas fa-user-plus me-1"></i>Créer un compte 
                            </a>
                        </p>
                        <p class="mb-0">
                            <small class="text-muted"> :</small><br>
                            <span class="badge bg-primary me-1"></span>
                            <span class="badge bg-danger"></span>
                        </p>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Connexion sécurisée • Identiguinée © 2026
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%) !important;
}
</style>
@endsection
