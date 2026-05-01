@extends('layouts.app')

@section('title', 'Nouvelle demande - Espace Citoyen')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Nouvelle demande</h1>
                <p class="mb-0">Faites une demande de document d'identité</p>
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
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('citizen.request.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Type de document *</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="document_type" 
                                               id="cni" value="cni" required>
                                        <label class="form-check-label" for="cni">
                                            <i class="fas fa-id-card me-2 text-primary"></i>
                                            <strong>Carte Nationale d'Identité</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="document_type" 
                                               id="passeport" value="passeport">
                                        <label class="form-check-label" for="passeport">
                                            <i class="fas fa-passport me-2 text-primary"></i>
                                            <strong>Passeport</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="document_type" 
                                               id="permis" value="permis">
                                        <label class="form-check-label" for="permis">
                                            <i class="fas fa-car me-2 text-primary"></i>
                                            <strong>Permis de conduire</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="document_type" 
                                               id="extrait" value="extrait">
                                        <label class="form-check-label" for="extrait">
                                            <i class="fas fa-file-alt me-2 text-primary"></i>
                                            <strong>Extrait de naissance</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">Informations personnelles</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">Prénom *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="{{ old('first_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="{{ old('last_name') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">Date de naissance *</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" 
                                       value="{{ old('birth_date') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birth_place" class="form-label">Lieu de naissance *</label>
                                <input type="text" class="form-control" id="birth_place" name="birth_place" 
                                       value="{{ old('birth_place') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse complète *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label">Numéro de téléphone *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}" required>
                        </div>

                        <hr>

                        <h5 class="mb-3">Documents à joindre</h5>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Veuillez préparer les documents suivants : photo d'identité, copie de l'acte de naissance, 
                            justificatif de domicile. Ces documents seront à télécharger lors de la finalisation de votre demande.
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="documents_ready" required>
                                <label class="form-check-label" for="documents_ready">
                                    Je confirme avoir tous les documents nécessaires et certifie l'exactitude des informations fournies
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Soumettre la demande
                            </button>
                            <a href="{{ route('citizen.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
