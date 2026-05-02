@extends('layouts.app')

@section('title', 'Tableau de bord - Espace Citoyen')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Tableau de bord</h1>
                <p class="mb-0">Bienvenue, {{ session('citizen_name') }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <form action="{{ route('citizen.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light">
                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-file-alt text-primary" style="font-size: 2rem;"></i>
                    <h5 class="mt-2">{{ count($requests) }}</h5>
                    <p class="text-muted mb-0">Mes demandes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                    <h5 class="mt-2">{{ collect($requests)->whereIn('status', ['approuvé', 'validée'])->count() }}</h5>
                    <p class="text-muted mb-0">Documents valides</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-download text-info" style="font-size: 2rem;"></i>
                    <h5 class="mt-2">{{ $documentsCount ?? 0 }}</h5>
                    <p class="text-muted mb-0">Documents disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-times-circle text-danger" style="font-size: 2rem;"></i>
                    <h5 class="mt-2">{{ collect($requests)->where('status', 'rejetée')->count() }}</h5>
                    <p class="text-muted mb-0">Rejetées</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes demandes récentes</h5>
                    <a href="{{ route('citizen.documents') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Voir tout
                    </a>
                </div>
                <div class="card-body">
                    @if(count($requests) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                    <tr>
                                        <td><strong>{{ $request['reference'] }}</strong></td>
                                        <td>{{ $request['document_type'] }}</td>
                                        <td>{{ $request['created_at'] }}</td>
                                        <td>
                                            @switch($request['status'])
                                                @case('approuvé')
                                                    <span class="status-badge status-validée">Généré</span>
                                                    @break
                                                @case('validée')
                                                    <span class="status-badge status-validée">Validé</span>
                                                    @break
                                                @case('en cours')
                                                    <span class="status-badge status-en-cours">En cours</span>
                                                    @break
                                                @case('rejetée')
                                                    <span class="status-badge status-rejetée">Rejetée</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($request['status'] === 'approuvé' || $request['status'] === 'validée')
                                                <a href="{{ route('citizen.download', $request['reference']) }}" 
                                                   class="btn btn-sm btn-success me-1">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="confirmDelete('{{ $request['reference'] }}', '{{ str_replace("'", "\\'", $request['document_type']) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Aucune demande pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('citizen.request.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nouvelle demande
                        </a>
                        <a href="{{ route('citizen.documents') }}" class="btn btn-outline-primary">
                            <i class="fas fa-folder me-2"></i>Mes documents
                        </a>
                        <a href="{{ route('citizen.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-edit me-2"></i>Mon profil
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Email :</strong> {{ session('citizen_email') }}</p>
                    <p class="mb-0"><strong>Statut :</strong> 
                        <span class="badge bg-success">Compte vérifié</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(reference, documentType) {
    console.log('Tentative de suppression:', reference, documentType);
    
    if (confirm('Êtes-vous sûr de vouloir supprimer ce ' + documentType + ' (Réf: ' + reference + ') ?\n\nCette action est irréversible.')) {
        console.log('Confirmation reçue, création du formulaire...');
        
        // Créer un formulaire pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/citoyen/document/' + reference + '/supprimer';
        form.style.display = 'none';
        
        // Ajouter le token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Ajouter la méthode DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        console.log('Formulaire créé:', form);
        
        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
    } else {
        console.log('Suppression annulée');
    }
}

// Vérifier que la fonction est bien chargée
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard chargé, fonction confirmDelete disponible:', typeof confirmDelete);
});
</script>
@endsection
