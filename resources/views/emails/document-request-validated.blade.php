@component('mail::message')
# Votre demande a été validée !

Félicitations {{ $notifiable->name }},

Votre demande de {{ $documentRequest->document_type_label }} a été validée avec succès !

**Référence :** {{ $documentRequest->reference }}

@if($document)
**Informations du document :**
- Date d'émission : {{ $document->issue_date->format('d/m/Y') }}
- Date d'expiration : {{ $document->expiry_date->format('d/m/Y') }}
- Code de vérification : {{ $document->qr_code }}
@endif

Votre document est maintenant disponible dans votre espace citoyen.

@component('mail::button', ['url' => route('citizen.documents')])
Télécharger mon document
@endcomponent

**Prochaines étapes :**
1. Téléchargez votre document depuis votre espace
2. Vérifiez que toutes les informations sont correctes
3. Conservez votre document en lieu sûr
4. Notez votre code de vérification pour les contrôles futurs

**Important :**
- Votre document est officiellement reconnu par les autorités guinéennes
- Toute tentative de falsification est passible de sanctions pénales
- En cas de perte ou de vol, vous pouvez demander un duplicata

Nous vous remercions de votre confiance dans nos services numériques.

Cordialement,  
L'équipe IdentiGuinée  
Ministère de l'Intérieur - République de Guinée
@endcomponent
