@extends('layouts.app')

@section('title', 'Mon profil - Espace Citoyen')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Mon profil</h1>
                <p class="mb-0">Consultez et modifiez vos informations personnelles</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('citizen.dashboard') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nom complet</label>
                                <p class="form-control-plaintext">{{ $citizen->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <p class="form-control-plaintext">{{ $citizen->email }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Téléphone</label>
                                <p class="form-control-plaintext">{{ $citizen->phone ?? 'Non spécifié' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Numéro CNI</label>
                                <p class="form-control-plaintext">{{ $citizen->cni_number ?? 'Non spécifié' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date de naissance</label>
                                <p class="form-control-plaintext">{{ $citizen->birth_date ? $citizen->birth_date->format('d/m/Y') : 'Non spécifiée' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Lieu de naissance</label>
                                <p class="form-control-plaintext">{{ $citizen->birth_place ?? 'Non spécifié' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Adresse</label>
                                <p class="form-control-plaintext">{{ $citizen->address ?? 'Non spécifiée' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Profession</label>
                                <p class="form-control-plaintext">{{ $citizen->profession ?? 'Non spécifiée' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nationalité</label>
                                <p class="form-control-plaintext">{{ $citizen->nationality }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('citizen.profile.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Modifier mon profil
                        </a>
                        <a href="{{ route('citizen.password.change') }}" class="btn btn-outline-warning">
                            <i class="fas fa-key me-2"></i>Changer le mot de passe
                        </a>
                        <a href="{{ route('citizen.documents') }}" class="btn btn-outline-info">
                            <i class="fas fa-folder me-2"></i>Voir mes documents
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Statut du compte</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="text-success">Compte vérifié</h6>
                    <p class="text-muted small mb-0">Votre compte est activé et vous pouvez utiliser tous les services</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
