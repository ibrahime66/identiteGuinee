@extends('layouts.app')

@section('title', 'Détails de la demande - Administration')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Détails de la demande</h1>
                <p class="mb-0">Référence : {{ $request['reference'] }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('admin.requests') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations de la demande</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Référence :</strong> {{ $request['reference'] }}
                        </div>
                        <div class="col-md-6">
                            <strong>Date de demande :</strong> {{ \Carbon\Carbon::parse($request['date'])->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Type de document :</strong> {{ $request['document_type'] }}
                        </div>
                        <div class="col-md-6">
                            <strong>Statut actuel :</strong> 
                            <span class="status-badge status-en-cours">En cours</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <strong>Notes :</strong> {{ $request['notes'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Nom complet :</strong> {{ $request['first_name'] }} {{ $request['last_name'] }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email :</strong> {{ $request['citizen_email'] }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Téléphone :</strong> {{ $request['citizen_phone'] }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date de naissance :</strong> {{ \Carbon\Carbon::parse($request['birth_date'])->format('d/m/Y') }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Lieu de naissance :</strong> {{ $request['birth_place'] }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Profession :</strong> {{ $request['profession'] }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Nationalité :</strong> {{ $request['nationality'] }}
                        </div>
                        <div class="col-12">
                            <strong>Adresse :</strong> {{ $request['address'] }}
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
                    @if($request['status'] === 'en cours')
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.request.validate', $request['id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" 
                                        onclick="return confirm('Valider cette demande ? Cette action est irréversible.')">
                                    <i class="fas fa-check me-2"></i>Valider la demande
                                </button>
                            </form>
                            
                            <button type="button" class="btn btn-danger w-100" 
                                    onclick="showRejectModal({{ $request['id'] }}, '{{ $request['reference'] }}')">
                                <i class="fas fa-times me-2"></i>Rejeter la demande
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Cette demande a déjà été traitée.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Documents joints</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-image me-2 text-primary"></i>
                            Photo d'identité
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-alt me-2 text-primary"></i>
                            Acte de naissance
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-home me-2 text-primary"></i>
                            Justificatif de domicile
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Historique</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Demande créée</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($request['date'])->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-warning text-white rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">En cours de traitement</h6>
                                <small class="text-muted">En attente de validation</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter la demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.request.reject', $request['id']) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Vous êtes sur le point de rejeter la demande <strong>{{ $request['reference'] }}</strong>.</p>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Motif du rejet *</label>
                        <textarea class="form-control" id="reason" name="reason" rows="4" required 
                                  placeholder="Veuillez expliquer la raison du rejet..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Rejeter la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showRejectModal(id, reference) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}
</script>
@endsection
