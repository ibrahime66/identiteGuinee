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
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-user fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Informations personnelles</h5>
                            <small class="opacity-75">Profil citoyen Identiguinée</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Informations principales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Nom complet</small>
                                        <span class="fw-bold text-dark">{{ $citizen->name }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Email</small>
                                        <span class="text-dark">{{ $citizen->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Téléphone</small>
                                        <span class="text-dark">{{ $citizen->phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Numéro CNI</small>
                                        <span class="fw-bold text-dark">{{ $citizen->cni_number }}</span>
                                        <br><small class="text-muted"><i class="fas fa-lock fa-xs"></i> Non modifiable</small>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 me-3">
                                        <i class="fas fa-birthday-cake"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Date de naissance</small>
                                        @if($citizen->birth_date)
                                            <span class="text-dark">{{ $citizen->birth_date->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-muted">Non spécifiée</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Lieu de naissance</small>
                                        @if($citizen->birth_place)
                                            <span class="text-dark">{{ $citizen->birth_place }}</span>
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations additionnelles -->
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-box bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 me-3">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Adresse</small>
                                            @if($citizen->address)
                                                <span class="text-dark">{{ $citizen->address }}</span>
                                            @else
                                                <span class="text-muted">Non spécifiée</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-box bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 me-3">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Profession</small>
                                            @if($citizen->profession)
                                                <span class="text-dark">{{ $citizen->profession }}</span>
                                            @else
                                                <span class="text-muted">Non spécifiée</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3">
                                            <i class="fas fa-flag"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Nationalité</small>
                                            <span class="text-dark">{{ $citizen->nationality }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
            .icon-box {
                width: 35px;
                height: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .info-item small {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                font-weight: 600;
            }
            .info-item span {
                font-size: 0.95rem;
            }
            .bg-gradient-primary {
                background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%) !important;
            }
            </style>
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
