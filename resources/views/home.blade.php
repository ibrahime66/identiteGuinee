@extends('layouts.app')

@section('title', 'IdentiGuinée - Plateforme Nationale d\'Identité Numérique')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">IdentiGuinée</h1>
                <h2 class="h3 mb-4">La solution numérique pour vos documents d'identité</h2>
                <p class="lead mb-4">
                    Simplifiez vos démarches administratives grâce à notre plateforme en ligne. 
                    Faites vos demandes, suivez leur traitement et obtenez vos documents d'identité 
                    en toute sécurité et rapidité.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('citizen.login') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-user me-2"></i>Espace Citoyen
                    </a>
                    <a href="{{ route('verifier.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-search me-2"></i>Vérifier un document
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-id-card" style="font-size: 15rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Problem Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Les défis actuels</h2>
                <p class="lead text-muted">La corruption et la lenteur administrative freinent l'accès aux services essentiels</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4>Délais excessifs</h4>
                    <p>Les demandes de documents peuvent prendre des mois, voire des années avant d'être traitées.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h4>Corruption systémique</h4>
                    <p>Les pots-de-vin et les pratiques corruptibles rendent l'accès aux services inéquitable.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>Papiers perdus</h4>
                    <p>Les dossiers papier se perdent facilement, créant des frustrations pour les citoyens.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Solution Section -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <h2 class="display-5 fw-bold mb-4">Notre solution digitale</h2>
                <p class="lead mb-4">
                    IdentiGuinée transforme complètement le processus de demande de documents d'identité 
                    grâce à la digitalisation et l'automatisation.
                </p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-3"></i>
                        <strong>100% en ligne :</strong> Plus besoin de se déplacer physiquement
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-3"></i>
                        <strong>Transparence totale :</strong> Suivez votre demande en temps réel
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-3"></i>
                        <strong>Sécurité renforcée :</strong> Protection des données personnelles
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-3"></i>
                        <strong>Rapidité :</strong> Traitement accéléré des demandes valides
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stats-card">
                            <div class="stats-number">-80%</div>
                            <p class="mb-0">Réduction des délais</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stats-card">
                            <div class="stats-number">100%</div>
                            <p class="mb-0">Transparence</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stats-card">
                            <div class="stats-number">24/7</div>
                            <p class="mb-0">Disponibilité</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stats-card">
                            <div class="stats-number">0</div>
                            <p class="mb-0">Corruption</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Fonctionnalités principales</h2>
                <p class="lead text-muted">Une plateforme complète pour tous vos besoins d'identité</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <h5>CNI Numérique</h5>
                    <p>Demandez votre carte nationale d'identité en quelques clics</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-passport"></i>
                    </div>
                    <h5>Passeport</h5>
                    <p>Obtenez votre passeport biométrique rapidement</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h5>Permis de conduire</h5>
                    <p>Demandez votre permis de conduire en ligne</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h5>Vérification instantanée</h5>
                    <p>Vérifiez l'authenticité des documents en temps réel</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it works Section -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Comment ça marche ?</h2>
                <p class="lead text-muted">Un processus simple en 4 étapes</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="process-step">
                    <div class="process-number">1</div>
                    <h5>Inscription</h5>
                    <p>Créez votre compte citoyen en quelques minutes</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="process-step">
                    <div class="process-number">2</div>
                    <h5>Demande</h5>
                    <p>Remplissez le formulaire en ligne et téléchargez vos documents</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="process-step">
                    <div class="process-number">3</div>
                    <h5>Suivi</h5>
                    <p>Suivez l'évolution de votre demande en temps réel</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="process-step">
                    <div class="process-number">4</div>
                    <h5>Réception</h5>
                    <p>Téléchargez votre document validé instantanément</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section-padding bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Prêt à commencer ?</h2>
        <p class="lead mb-4">Rejoignez des milliers de Guinéens qui utilisent déjà IdentiGuinée</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('citizen.register') }}" class="btn btn-light btn-lg">
                <i class="fas fa-user-plus me-2"></i>Créer mon compte
            </a>
            <a href="{{ route('admin.login') }}" class="btn btn-outline-light btn-lg">
                <i class="fas fa-user-shield me-2"></i>Espace Administration
            </a>
        </div>
    </div>
</section>
@endsection
