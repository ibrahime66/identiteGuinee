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

                        <!-- Champs spécifiques selon le type de document -->
                        <div id="specific-fields">
                            <!-- Champs pour Extrait de naissance -->
                            <div id="extrait-fields" style="display: none;" class="mb-4">
                                <hr>
                                <h5 class="mb-3">Informations pour l'extrait de naissance</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="father_name" class="form-label">Nom du père *</label>
                                        <input type="text" class="form-control" id="father_name" name="father_name" 
                                               value="{{ old('father_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mother_name" class="form-label">Nom de la mère *</label>
                                        <input type="text" class="form-control" id="mother_name" name="mother_name" 
                                               value="{{ old('mother_name') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="father_profession" class="form-label">Profession du père</label>
                                        <input type="text" class="form-control" id="father_profession" name="father_profession" 
                                               value="{{ old('father_profession') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mother_profession" class="form-label">Profession de la mère</label>
                                        <input type="text" class="form-control" id="mother_profession" name="mother_profession" 
                                               value="{{ old('mother_profession') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="declaration_date" class="form-label">Date de déclaration</label>
                                        <input type="date" class="form-control" id="declaration_date" name="declaration_date" 
                                               value="{{ old('declaration_date') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="declaration_place" class="form-label">Lieu de déclaration</label>
                                        <input type="text" class="form-control" id="declaration_place" name="declaration_place" 
                                               value="{{ old('declaration_place') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Champs pour CNI -->
                            <div id="cni-fields" style="display: none;" class="mb-4">
                                <hr>
                                <h5 class="mb-3">Informations pour la CNI</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="profession" class="form-label">Profession *</label>
                                        <input type="text" class="form-control" id="profession" name="profession" 
                                               value="{{ old('profession') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="height" class="form-label">Taille (cm)</label>
                                        <input type="number" class="form-control" id="height" name="height" 
                                               value="{{ old('height') }}" min="100" max="250">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="distinguishing_marks" class="form-label">Signes particuliers</label>
                                        <input type="text" class="form-control" id="distinguishing_marks" name="distinguishing_marks" 
                                               value="{{ old('distinguishing_marks') }}" placeholder="Tatouages, cicatrices, etc.">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="has_previous_cni" class="form-label">Type de demande *</label>
                                        <select class="form-control" id="has_previous_cni" name="has_previous_cni" onchange="togglePreviousCNI()">
                                            <option value="">Sélectionnez...</option>
                                            <option value="first">Première demande</option>
                                            <option value="renewal">Renouvellement</option>
                                            <option value="duplicate">Duplicata</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" id="previous_cni_row" style="display: none;">
                                    <div class="col-md-12 mb-3">
                                        <label for="previous_cni" class="form-label">Ancien numéro CNI</label>
                                        <input type="text" class="form-control" id="previous_cni" name="previous_cni" 
                                               value="{{ old('previous_cni') }}" placeholder="Ex: CNI-2020-000001">
                                    </div>
                                </div>
                            </div>

                            <!-- Champs pour Passeport -->
                            <div id="passeport-fields" style="display: none;" class="mb-4">
                                <hr>
                                <h5 class="mb-3">Informations pour le passeport</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="profession" class="form-label">Profession *</label>
                                        <input type="text" class="form-control" id="profession_passport" name="profession_passport" 
                                               value="{{ old('profession_passport') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="travel_purpose" class="form-label">Motif de voyage *</label>
                                        <select class="form-control" id="travel_purpose" name="travel_purpose">
                                            <option value="">Sélectionnez...</option>
                                            <option value="tourisme">Tourisme</option>
                                            <option value="affaires">Affaires</option>
                                            <option value="etudes">Études</option>
                                            <option value="medical">Médical</option>
                                            <option value="family">Familial</option>
                                            <option value="work">Travail</option>
                                            <option value="other">Autre</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="destination_countries" class="form-label">Pays de destination</label>
                                        <input type="text" class="form-control" id="destination_countries" name="destination_countries" 
                                               value="{{ old('destination_countries') }}" placeholder="Ex: France, Belgique, Sénégal">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="has_previous_passport" class="form-label">Type de demande *</label>
                                        <select class="form-control" id="has_previous_passport" name="has_previous_passport" onchange="togglePreviousPassport()">
                                            <option value="">Sélectionnez...</option>
                                            <option value="first">Première demande</option>
                                            <option value="renewal">Renouvellement</option>
                                            <option value="duplicate">Duplicata</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" id="previous_passport_row" style="display: none;">
                                    <div class="col-md-12 mb-3">
                                        <label for="previous_passport" class="form-label">Ancien numéro passeport</label>
                                        <input type="text" class="form-control" id="previous_passport" name="previous_passport" 
                                               value="{{ old('previous_passport') }}" placeholder="Ex: P00000000">
                                    </div>
                                </div>
                            </div>

                            <!-- Champs pour Permis de conduire -->
                            <div id="permis-fields" style="display: none;" class="mb-4">
                                <hr>
                                <h5 class="mb-3">Informations pour le permis de conduire</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="license_category" class="form-label">Catégorie demandée *</label>
                                        <select class="form-control" id="license_category" name="license_category">
                                            <option value="">Sélectionnez...</option>
                                            <option value="A">A - Moto</option>
                                            <option value="B">B - Voiture</option>
                                            <option value="C">C - Poids lourd</option>
                                            <option value="D">D - Transport de personnes</option>
                                            <option value="E">E - Remorque</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="driving_experience" class="form-label">Années d'expérience</label>
                                        <input type="number" class="form-control" id="driving_experience" name="driving_experience" 
                                               value="{{ old('driving_experience') }}" min="0" max="50">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="license_issue_country" class="form-label">Pays de délivrance</label>
                                        <input type="text" class="form-control" id="license_issue_country" name="license_issue_country" 
                                               value="{{ old('license_issue_country') }}" placeholder="Guinée">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="has_previous_license" class="form-label">Type de demande *</label>
                                        <select class="form-control" id="has_previous_license" name="has_previous_license" onchange="togglePreviousLicense()">
                                            <option value="">Sélectionnez...</option>
                                            <option value="first">Première demande</option>
                                            <option value="renewal">Renouvellement</option>
                                            <option value="duplicate">Duplicata</option>
                                            <option value="exchange">Échange permis étranger</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" id="previous_license_row" style="display: none;">
                                    <div class="col-md-12 mb-3">
                                        <label for="previous_license" class="form-label">Ancien numéro de permis</label>
                                        <input type="text" class="form-control" id="previous_license" name="previous_license" 
                                               value="{{ old('previous_license') }}" placeholder="Ex: 1234567890">
                                    </div>
                                </div>
                            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const documentTypeRadios = document.querySelectorAll('input[name="document_type"]');
    const specificFields = document.getElementById('specific-fields');
    
    function showSpecificFields(type) {
        // Masquer tous les champs spécifiques
        document.getElementById('extrait-fields').style.display = 'none';
        document.getElementById('cni-fields').style.display = 'none';
        document.getElementById('passeport-fields').style.display = 'none';
        document.getElementById('permis-fields').style.display = 'none';
        
        // Afficher les champs correspondants
        if (type) {
            const fieldId = type + '-fields';
            const fieldElement = document.getElementById(fieldId);
            if (fieldElement) {
                fieldElement.style.display = 'block';
            }
        }
    }
    
    documentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            showSpecificFields(this.value);
        });
    });
    
    // Afficher les champs si un type est déjà sélectionné
    const selectedType = document.querySelector('input[name="document_type"]:checked');
    if (selectedType) {
        showSpecificFields(selectedType.value);
    }
});

// Fonctions pour gérer l'affichage des champs "Ancien numéro"
function togglePreviousCNI() {
    const select = document.getElementById('has_previous_cni');
    const row = document.getElementById('previous_cni_row');
    const input = document.getElementById('previous_cni');
    
    if (select.value === 'renewal' || select.value === 'duplicate') {
        row.style.display = 'block';
        input.required = true;
    } else {
        row.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

function togglePreviousPassport() {
    const select = document.getElementById('has_previous_passport');
    const row = document.getElementById('previous_passport_row');
    const input = document.getElementById('previous_passport');
    
    if (select.value === 'renewal' || select.value === 'duplicate') {
        row.style.display = 'block';
        input.required = true;
    } else {
        row.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

function togglePreviousLicense() {
    const select = document.getElementById('has_previous_license');
    const row = document.getElementById('previous_license_row');
    const input = document.getElementById('previous_license');
    
    if (select.value === 'renewal' || select.value === 'duplicate' || select.value === 'exchange') {
        row.style.display = 'block';
        input.required = true;
    } else {
        row.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}
</script>
@endsection
