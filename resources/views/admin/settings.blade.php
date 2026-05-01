@extends('layouts.app')

@section('title', 'Paramètres - Administration')

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Paramètres</h1>
                <p class="mb-0">Configurez les paramètres du système</p>
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
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Paramètres généraux</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="app_name" class="form-label">Nom de l'application</label>
                                    <input type="text" class="form-control" id="app_name" value="{{ $settings['app_name'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="app_version" class="form-label">Version</label>
                                    <input type="text" class="form-control" id="app_version" value="{{ $settings['app_version'] }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_requests_per_day" class="form-label">Max demandes/jour par citoyen</label>
                                    <input type="number" class="form-control" id="max_requests_per_day" value="{{ $settings['max_requests_per_day'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notification_email" class="form-label">Email de notification</label>
                                    <input type="email" class="form-control" id="notification_email" value="{{ $settings['notification_email'] }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="auto_validation_enabled" {{ $settings['auto_validation_enabled'] ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="auto_validation_enabled">
                                        Validation automatique
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" {{ $settings['maintenance_mode'] ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="maintenance_mode">
                                        Mode maintenance
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Les paramètres sont actuellement en lecture seule. Contactez l'administrateur système pour les modifier.
                        </div>

                        <button type="submit" class="btn btn-primary" disabled>
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations système</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Version Laravel:</strong>
                        <p class="mb-0">{{ $systemInfo['laravel_version'] }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Version PHP:</strong>
                        <p class="mb-0">{{ $systemInfo['php_version'] }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Environnement:</strong>
                        <p class="mb-0">
                            <span class="badge bg-{{ $systemInfo['environment'] == 'local' ? 'warning' : 'success' }}">
                                {{ $systemInfo['environment'] }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <strong>Base de données:</strong>
                        <p class="mb-0">{{ ucfirst($systemInfo['database']) }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Fuseau horaire:</strong>
                        <p class="mb-0">{{ $systemInfo['timezone'] }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Mode debug:</strong>
                        <p class="mb-0">
                            <span class="badge bg-{{ $systemInfo['debug_mode'] ? 'danger' : 'success' }}">
                                {{ $systemInfo['debug_mode'] ? 'Activé' : 'Désactivé' }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <strong>URL de l'application:</strong>
                        <p class="mb-0">{{ $systemInfo['url'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Actions système</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('admin.settings.clear-cache') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning w-100" onclick="return confirm('Êtes-vous sûr de vouloir vider le cache ?')">
                                <i class="fas fa-broom me-2"></i>Vider le cache
                            </button>
                        </form>
                        <a href="{{ route('admin.settings.export') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-download me-2"></i>Exporter les données
                        </a>
                        <form method="POST" action="{{ route('admin.settings.backup') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100" onclick="return confirm('Êtes-vous sûr de vouloir sauvegarder la base de données ?')">
                                <i class="fas fa-sync me-2"></i>Sauvegarder la base
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
