@extends('layouts.app')

@section('title', 'Mes documents - Espace Citoyen')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Mes documents</h1>
                <p class="mb-0">Consultez et téléchargez vos documents validés</p>
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
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Documents disponibles</h5>
                    <a href="{{ route('citizen.request.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle demande
                    </a>
                </div>
                <div class="card-body">
                    @if(count($documents) > 0)
                        <div class="row">
                            @foreach($documents as $document)
                            <div class="col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            @switch($document['type'])
                                                @case('Carte Nationale d\'Identité')
                                                    <i class="fas fa-id-card text-primary" style="font-size: 3rem;"></i>
                                                    @break
                                                @case('Passeport')
                                                    <i class="fas fa-passport text-primary" style="font-size: 3rem;"></i>
                                                    @break
                                                @case('Permis de conduire')
                                                    <i class="fas fa-car text-primary" style="font-size: 3rem;"></i>
                                                    @break
                                            @endswitch
                                        </div>
                                        <h6 class="card-title">{{ $document['type'] }}</h6>
                                        <p class="text-muted small mb-2">
                                            <strong>Référence :</strong> {{ $document['reference'] }}
                                        </p>
                                        <p class="text-muted small mb-2">
                                            <strong>Date d'émission :</strong> {{ \Carbon\Carbon::parse($document['issue_date'])->format('d/m/Y') }}
                                        </p>
                                        <p class="text-muted small mb-3">
                                            <strong>Expiration :</strong> {{ \Carbon\Carbon::parse($document['expiry_date'])->format('d/m/Y') }}
                                        </p>
                                        <span class="badge bg-success mb-3">{{ $document['status'] }}</span>
                                        <div class="d-grid">
                                            <a href="{{ route('citizen.download', $document['id']) }}" 
                                               class="btn btn-primary">
                                                <i class="fas fa-download me-2"></i>Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 text-muted">Aucun document disponible</h4>
                            <p class="text-muted">Vous n'avez pas encore de documents validés.</p>
                            <a href="{{ route('citizen.request.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Faire une demande
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
