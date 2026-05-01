@extends('layouts.app')

@section('title', 'Résultat de vérification - IdentiGuinée')

@section('content')
<div class="container section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header text-center py-4">
                    @if($document['valid'])
                        <div class="text-success">
                            <i class="fas fa-check-circle" style="font-size: 3rem;"></i>
                            <h3 class="mt-3">Document Valide</h3>
                            <p class="mb-0">Ce document est authentique et en cours de validité</p>
                        </div>
                    @else
                        <div class="text-danger">
                            <i class="fas fa-times-circle" style="font-size: 3rem;"></i>
                            <h3 class="mt-3">Document Invalide</h3>
                            <p class="mb-0">Ce document n'a pas été trouvé dans notre base de données</p>
                        </div>
                    @endif
                </div>
                
                <div class="card-body p-5">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Code vérifié :</strong>
                            <p class="text-primary">{{ $documentCode }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Date de vérification :</strong>
                            <p>{{ $document['verification_date'] ?? now()->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    @if($document['valid'])
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Type de document :</strong>
                                    <p>{{ $document['type'] }}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Nom du titulaire :</strong>
                                    <p>{{ $document['holder_name'] }}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Date de naissance :</strong>
                                    <p>{{ \Carbon\Carbon::parse($document['birth_date'])->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Lieu de naissance :</strong>
                                    <p>{{ $document['birth_place'] }}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Date d'émission :</strong>
                                    <p>{{ \Carbon\Carbon::parse($document['issue_date'])->format('d/m/Y') }}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Date d'expiration :</strong>
                                    <p>{{ \Carbon\Carbon::parse($document['expiry_date'])->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success mt-4">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Authenticité confirmée</strong> - Ce document a été délivré par les autorités 
                            compétentes de la République de Guinée et est actuellement valide.
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Le code "{{ $documentCode }}" ne correspond à aucun document 
                            valide dans notre base de données. Veuillez vérifier le code et réessayer.
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Possibles causes :</h5>
                                <ul>
                                    <li>Code incorrect ou mal saisi</li>
                                    <li>Document périmé</li>
                                    <li>Document jamais délivré</li>
                                    <li>Document contrefait</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Recommandations :</h5>
                                <ul>
                                    <li>Vérifiez l'orthographe du code</li>
                                    <li>Confirmez la date d'expiration</li>
                                    <li>Contactez les autorités si nécessaire</li>
                                    <li>Signalez tout document suspect</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('verifier.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Nouvelle vérification
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body text-center">
                    <p class="mb-2 text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        Cette vérification a été effectuée via la plateforme officielle IdentiGuinée
                    </p>
                    <small class="text-muted">
                        Pour toute question ou signalement, contactez-nous : verification@identiguinee.gn
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
