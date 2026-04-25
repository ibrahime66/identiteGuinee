@extends('layouts.app')

@section('title', 'Vérification de documents - IdentiGuinée')

@section('content')
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Vérification de documents</h1>
                <p class="lead mb-4">
                    Vérifiez instantanément l'authenticité des documents d'identité 
                    délivrés par la République de Guinée.
                </p>
                <p class="mb-4">
                    Entrez le code unique du document ou scannez le QR code pour 
                    obtenir une vérification immédiate.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-qrcode" style="font-size: 12rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-search text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Vérifier un document</h3>
                        <p class="text-muted">Entrez le code de vérification du document</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('verifier.verify') }}" method="POST" id="verifyForm">
                        @csrf
                        <div class="mb-4">
                            <label for="document_code" class="form-label fw-bold">Code du document *</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="document_code" 
                                       name="document_code" 
                                       placeholder="Ex: CNI-2024-001234"
                                       required
                                       autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary" onclick="simulateScan()">
                                    <i class="fas fa-camera"></i> Scanner
                                </button>
                            </div>
                            <div class="form-text">
                                Le code se trouve généralement au dos du document ou dans le QR code.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>Vérifier le document
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="feature-icon-small mb-3">
                                <i class="fas fa-bolt text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <h6>Vérification instantanée</h6>
                            <p class="text-muted small">Résultats en quelques secondes</p>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-icon-small mb-3">
                                <i class="fas fa-shield-alt text-success" style="font-size: 2rem;"></i>
                            </div>
                            <h6>100% sécurisé</h6>
                            <p class="text-muted small">Connexion cryptée et protégée</p>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-icon-small mb-3">
                                <i class="fas fa-database text-info" style="font-size: 2rem;"></i>
                            </div>
                            <h6>Base de données officielle</h6>
                            <p class="text-muted small">Accès direct aux registres nationaux</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Codes de test</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Utilisez ces codes pour tester la vérification :</p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <code>CNI-2024-001234</code>
                                <p class="text-muted small mb-0 mt-2">Carte d'identité valide</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <code>PAS-2024-000567</code>
                                <p class="text-muted small mb-0 mt-2">Passeport valide</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <code>PER-2024-000890</code>
                                <p class="text-muted small mb-0 mt-2">Permis valide</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.feature-icon-small {
    width: 60px;
    height: 60px;
    background: var(--bg-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
</style>
@endsection

@section('scripts')
<script>
function simulateScan() {
    const codes = ['CNI-2024-001234', 'PAS-2024-000567', 'PER-2024-000890'];
    const randomCode = codes[Math.floor(Math.random() * codes.length)];
    
    document.getElementById('document_code').value = randomCode;
    
    // Simulate scanning animation
    const input = document.getElementById('document_code');
    input.style.backgroundColor = '#fff3cd';
    setTimeout(() => {
        input.style.backgroundColor = '';
        input.focus();
    }, 500);
}

// Auto-focus on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('document_code').focus();
});

// Handle form submission with loading state
document.getElementById('verifyForm').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Vérification en cours...';
    submitBtn.disabled = true;
});
</script>
@endsection
