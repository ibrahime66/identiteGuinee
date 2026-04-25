@extends('layouts.app')

@section('title', 'Tableau de bord - Administration')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Tableau de bord administrateur</h1>
                <p class="mb-0">Bienvenue, {{ session('admin_name') }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
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
        <div class="col-lg-2 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-file-alt text-primary" style="font-size: 2rem;"></i>
                    <h4 class="mt-2">{{ $stats['total_requests'] }}</h4>
                    <p class="text-muted mb-0">Total demandes</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clock text-warning" style="font-size: 2rem;"></i>
                    <h4 class="mt-2">{{ $stats['pending_requests'] }}</h4>
                    <p class="text-muted mb-0">En attente</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle text-success" style="font-size: 2rem;"></i>
                    <h4 class="mt-2">{{ $stats['validated_requests'] }}</h4>
                    <p class="text-muted mb-0">Validées</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-times-circle text-danger" style="font-size: 2rem;"></i>
                    <h4 class="mt-2">{{ $stats['rejected_requests'] }}</h4>
                    <p class="text-muted mb-0">Rejetées</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-day text-info" style="font-size: 2rem;"></i>
                    <h4 class="mt-2">{{ $stats['today_requests'] }}</h4>
                    <p class="text-muted mb-0">Aujourd'hui</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-percentage text-secondary" style="font-size: 2rem;"></i>
                    <h4 class="mt-2">{{ round(($stats['validated_requests'] / $stats['total_requests']) * 100, 1) }}%</h4>
                    <p class="text-muted mb-0">Taux validation</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Demandes récentes</h5>
                    <a href="{{ route('admin.requests') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Voir tout
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Citoyen</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRequests as $request)
                                <tr>
                                    <td><strong>{{ $request['reference'] }}</strong></td>
                                    <td>{{ $request['citizen_name'] }}</td>
                                    <td>{{ $request['document_type'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($request['date'])->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="status-badge status-en-cours">En cours</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.request.show', $request['id']) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                        <a href="{{ route('admin.requests') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>Toutes les demandes
                        </a>
                        <a href="{{ route('admin.requests') }}?status=pending" class="btn btn-warning">
                            <i class="fas fa-clock me-2"></i>En attente ({{ $stats['pending_requests'] }})
                        </a>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-chart-bar me-2"></i>Rapports
                        </button>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-cog me-2"></i>Paramètres
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Activité récente</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-success text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Demande validée</h6>
                                <p class="text-muted small mb-0">CNI-2024-001235 - Fatoumata Touré</p>
                                <small class="text-muted">Il y a 2 heures</small>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Nouvelle demande</h6>
                                <p class="text-muted small mb-0">PAS-2024-000567 - Aïssatou Bah</p>
                                <small class="text-muted">Il y a 3 heures</small>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-danger text-white rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Demande rejetée</h6>
                                <p class="text-muted small mb-0">PER-2024-000891 - Mohamed Camara</p>
                                <small class="text-muted">Il y a 5 heures</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
