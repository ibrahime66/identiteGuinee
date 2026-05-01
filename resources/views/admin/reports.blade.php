@extends('layouts.app')

@section('title', 'Rapports - Administration')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Rapports</h1>
                <p class="mb-0">Consultez les statistiques et rapports du système</p>
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
    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_requests'] }}</h4>
                            <p class="mb-0">Total demandes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['pending_requests'] }}</h4>
                            <p class="mb-0">En attente</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['validated_requests'] }}</h4>
                            <p class="mb-0">Validées</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['rejected_requests'] }}</h4>
                            <p class="mb-0">Rejetées</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Documents</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-primary">{{ $stats['total_documents'] }}</h3>
                            <p class="text-muted">Total documents</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">{{ $stats['valid_documents'] }}</h3>
                            <p class="text-muted">Documents valides</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Utilisateurs</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $stats['total_users'] }}</h3>
                    <p class="text-muted">Citoyens inscrits</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Taux de validation</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success">
                        @if($stats['total_requests'] > 0)
                            {{ round(($stats['validated_requests'] / $stats['total_requests']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </h3>
                    <p class="text-muted">Demandes validées</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Demandes par type de document</h5>
                </div>
                <div class="card-body">
                    @if($requestsByType->count() > 0)
                        @foreach($requestsByType as $type => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ App\Http\Controllers\AdminController::getDocumentTypeLabelStatic($type) }}</span>
                                <div>
                                    <div class="progress" style="width: 100px; height: 20px;">
                                        <div class="progress-bar" style="width: {{ ($count / $stats['total_requests']) * 100 }}%"></div>
                                    </div>
                                    <span class="ms-2">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Aucune demande</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Demandes des 6 derniers mois</h5>
                </div>
                <div class="card-body">
                    @if($monthlyStats->count() > 0)
                        @foreach($monthlyStats as $stat)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $stat->month)->format('F Y') }}</span>
                                <span class="badge bg-primary">{{ $stat->count }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Aucune demande</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
