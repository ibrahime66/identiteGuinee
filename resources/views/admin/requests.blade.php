@extends('layouts.app')

@section('title', 'Gestion des demandes - Administration')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Gestion des demandes</h1>
                <p class="mb-0">Consultez et traitez toutes les demandes de documents</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Retour au tableau de bord
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

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Liste des demandes</h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="filterRequests('all')">Toutes</button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="filterRequests('pending')">En attente</button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="filterRequests('validated')">Validées</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="filterRequests('rejected')">Rejetées</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="requestsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Référence</th>
                            <th>Citoyen</th>
                            <th>Contact</th>
                            <th>Type document</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr data-status="{{ $request['status'] }}">
                            <td><strong>{{ $request['reference'] }}</strong></td>
                            <td>{{ $request['citizen_name'] }}</td>
                            <td>
                                <small>
                                    <div>{{ $request['citizen_email'] }}</div>
                                    <div>{{ $request['citizen_phone'] ?? 'Non spécifié' }}</div>
                                </small>
                            </td>
                            <td>{{ $request['document_type'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($request['date'])->format('d/m/Y') }}</td>
                            <td>
                                @switch($request['status'])
                                    @case('en cours')
                                        <span class="status-badge status-en-cours">En cours</span>
                                        @break
                                    @case('validée')
                                        <span class="status-badge status-validée">Validée</span>
                                        @break
                                    @case('rejetée')
                                        <span class="status-badge status-rejetée">Rejetée</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($request['priority'] === 'urgent')
                                    <span class="priority-urgent status-badge">Urgent</span>
                                @else
                                    <span class="priority-normal status-badge">Normal</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.request.show', $request['id']) }}" 
                                       class="btn btn-sm btn-primary" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request['status'] === 'en cours')
                                        <form action="{{ route('admin.request.validate', $request['id']) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    title="Valider la demande"
                                                    onclick="return confirm('Valider cette demande ?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                title="Rejeter la demande"
                                                onclick="showRejectModal({{ $request['id'] }}, '{{ $request['reference'] }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Vous êtes sur le point de rejeter la demande <strong id="rejectReference"></strong>.</p>
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
let currentRequestId = null;

function filterRequests(status) {
    const rows = document.querySelectorAll('#requestsTable tbody tr');
    
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else {
            const rowStatus = row.getAttribute('data-status');
            row.style.display = rowStatus === status ? '' : 'none';
        }
    });

    // Update button styles
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

function showRejectModal(id, reference) {
    currentRequestId = id;
    document.getElementById('rejectReference').textContent = reference;
    document.getElementById('rejectForm').action = `/administration/demande/${id}/rejeter`;
    
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}
</script>
@endsection
