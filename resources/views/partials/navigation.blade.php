<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fas fa-id-card me-2"></i>IdentiGuinée
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('citizen.login') }}">Espace Citoyen</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.login') }}">Administration</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('verifier.index') }}">Vérification</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
