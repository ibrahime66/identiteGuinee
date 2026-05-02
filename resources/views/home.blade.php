@extends('layouts.app')

@section('title', 'IdentiGuinée - Plateforme Nationale d\'Identité Numérique')

@section('content')
<style>
.hero-section {
    background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
    color: white;
    padding: 120px 0 80px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.hero-subtitle {
    font-size: 1.5rem;
    font-weight: 300;
    margin-bottom: 1.5rem;
    opacity: 0.9;
}

.hero-description {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.8;
    line-height: 1.6;
}

.hero-buttons {
    gap: 1rem;
}

.btn-hero {
    padding: 12px 30px;
    font-size: 1.1rem;
    font-weight: 500;
    border-radius: 50px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-hero-primary {
    background: white;
    color: #0066cc;
}

.btn-hero-primary:hover {
    background: #f8f9fa;
    color: #0052a3;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn-hero-outline {
    background: transparent;
    color: white;
    border-color: white;
}

.btn-hero-outline:hover {
    background: white;
    color: #0066cc;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.hero-illustration {
    position: relative;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    transform: perspective(1000px) rotateY(-5deg);
}

.hero-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: white;
}

.section-padding {
    padding: 80px 0;
}

.feature-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
    border: 1px solid #e9ecef;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.feature-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
    background: linear-gradient(135deg, #0066cc, #0052a3);
}

.feature-card h5 {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.feature-card p {
    color: #6c757d;
    margin-bottom: 0;
}

.process-step {
    text-align: center;
    padding: 2rem;
    position: relative;
}

.process-number {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0066cc, #0052a3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 auto 1.5rem;
}

.process-step h5 {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.process-step p {
    color: #6c757d;
    margin-bottom: 0;
}

.stats-card {
    background: linear-gradient(135deg, #0066cc, #0052a3);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-card p {
    margin-bottom: 0;
    opacity: 0.9;
}

.access-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.access-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.access-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 3rem;
}

.access-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.access-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    position: relative;
    overflow: hidden;
}

.access-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0066cc, #0052a3);
}

.access-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.access-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
}

.access-icon-citizen {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.access-icon-admin {
    background: linear-gradient(135deg, #dc3545, #c82333);
}

.access-icon-verify {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

.access-card h3 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.access-content {
    margin-bottom: 1.5rem;
}

.access-card h3 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    color: #2c3e50;
}

.access-description {
    color: #6c757d;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.access-features {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
    text-align: left;
}

.access-features li {
    margin-bottom: 0.5rem;
    color: #495057;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
}

.access-features i {
    margin-right: 0.5rem;
    font-size: 0.8rem;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-info {
    color: #17a2b8 !important;
}

.access-action {
    margin-top: 1rem;
}

.access-link {
    color: #0066cc;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border-radius: 8px;
    background: rgba(0, 102, 204, 0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 102, 204, 0.2);
}

.access-link:hover {
    color: #0052a3;
    background: rgba(0, 102, 204, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 102, 204, 0.2);
}

.access-link i {
    transition: transform 0.3s ease;
}

.access-link:hover i {
    transform: translateX(3px);
}

.stats-section {
    padding: 80px 0;
    background: white;
}

.stats-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
    text-align: center;
}

.stats-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 3rem;
    text-align: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.stat-item {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 15px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}

.stat-number {
    font-size: 3rem;
    font-weight: 700;
    color: #0066cc;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    color: #6c757d;
    font-weight: 500;
}

.cta-section {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 2rem;
}

.cta-buttons {
    gap: 1rem;
    justify-content: center;
}

.btn-cta {
    padding: 15px 40px;
    font-size: 1.1rem;
    font-weight: 500;
    border-radius: 50px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
}

.btn-cta-primary {
    background: white;
    color: #2c3e50;
}

.btn-cta-primary:hover {
    background: #f8f9fa;
    color: #2c3e50;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.btn-cta-outline {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-cta-outline:hover {
    background: white;
    color: #2c3e50;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .hero-illustration {
        height: 300px;
    }
    
    .access-grid,
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-title {
        font-size: 2rem;
    }
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">IdentiGuinée</h1>
                    <h2 class="hero-subtitle">Plateforme Nationale d'Identité Numérique</h2>
                    <p class="hero-description">
                        La solution numérique moderne et sécurisée pour la gestion de vos documents d'identité. 
                        Accédez rapidement à tous les services administratifs en quelques clics seulement.
                    </p>
                    <div class="hero-buttons d-flex gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="btn-hero btn-hero-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </a>
                        <a href="{{ route('citizen.register') }}" class="btn-hero btn-hero-outline">
                            <i class="fas fa-user-plus me-2"></i>Créer un compte
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-illustration">
                    <div class="hero-card">
                        <div class="hero-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h4>Accès Sécurisé</h4>
                        <p>Plateforme certifiée par l'État</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Access Section -->
<section class="access-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="access-title">Services disponibles</h2>
            <p class="access-subtitle">Choisissez le service dont vous avez besoin</p>
        </div>
        
        <div class="access-grid">
            <div class="access-card">
                <div class="access-icon access-icon-citizen">
                    <i class="fas fa-user"></i>
                </div>
                <div class="access-content">
                    <h3>Espace Citoyen</h3>
                    <p class="access-description">Demandez et téléchargez vos documents d'identité en toute simplicité</p>
                    <ul class="access-features">
                        <li><i class="fas fa-check-circle text-success"></i> Demande de documents</li>
                        <li><i class="fas fa-check-circle text-success"></i> Téléchargement instantané</li>
                        <li><i class="fas fa-check-circle text-success"></i> Suivi de demande</li>
                    </ul>
                </div>
                <div class="access-action">
                    <a href="{{ route('login') }}" class="access-link">
                        <i class="fas fa-arrow-right"></i>Accéder à l'espace
                    </a>
                </div>
            </div>
            
                        
            <div class="access-card">
                <div class="access-icon access-icon-verify">
                    <i class="fas fa-search"></i>
                </div>
                <div class="access-content">
                    <h3>Vérification</h3>
                    <p class="access-description">Vérifiez l'authenticité des documents générés</p>
                    <ul class="access-features">
                        <li><i class="fas fa-check-circle text-info"></i> Scan QR code</li>
                        <li><i class="fas fa-check-circle text-info"></i> Validation instantanée</li>
                        <li><i class="fas fa-check-circle text-info"></i> Authentification officielle</li>
                    </ul>
                </div>
                <div class="access-action">
                    <a href="{{ route('verifier.index') }}" class="access-link">
                        <i class="fas fa-arrow-right"></i>Vérifier un document
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="stats-title">Chiffres clés</h2>
            <p class="stats-subtitle">Une plateforme au service de tous les Guinéens</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Disponibilité</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Numérique</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">5min</div>
                <div class="stat-label">Génération</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">Sécurisé</div>
                <div class="stat-label">Certifié</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Prêt à simplifier vos démarches ?</h2>
        <p class="cta-subtitle">Rejoignez la plateforme nationale d'identité numérique</p>
        <div class="cta-buttons d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('citizen.register') }}" class="btn-cta btn-cta-primary">
                <i class="fas fa-user-plus me-2"></i>Créer mon compte
            </a>
            <a href="{{ route('login') }}" class="btn-cta btn-cta-outline">
                <i class="fas fa-sign-in-alt me-2"></i>Me connecter
            </a>
        </div>
    </div>
</section>

<!-- Problems Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Les problèmes actuels</h2>
                <p class="lead text-muted">Les défis auxquels les citoyens guinéens sont confrontés</p>
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

<!-- Final CTA Section -->
<section class="section-padding bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Prêt à commencer ?</h2>
        <p class="lead mb-4">Rejoignez des milliers de Guinéens qui utilisent déjà IdentiGuinée</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('citizen.register') }}" class="btn btn-light btn-lg">
                <i class="fas fa-user-plus me-2"></i>Créer mon compte
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>Me connecter
            </a>
        </div>
    </div>
</section>

@endsection
