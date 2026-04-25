@component('mail::message')
# Confirmation de votre demande de document

Bonjour {{ $notifiable->name }},

Nous vous confirmons la réception de votre demande de {{ $documentRequest->document_type_label }}.

**Référence de votre demande :** {{ $documentRequest->reference }}

Votre demande est maintenant en cours de traitement par nos services. Vous pouvez suivre l'évolution de votre demande en vous connectant à votre espace citoyen.

@component('mail::button', ['url' => route('citizen.dashboard')])
Suivre ma demande
@endcomponent

**Informations importantes :**
- Le délai de traitement moyen est de 3 à 5 jours ouvrés
- Vous recevrez une notification par email dès que votre demande sera traitée
- Tous les documents sont vérifiés conformément aux réglementations en vigueur

Pour toute question, n'hésitez pas à nous contacter :
- Email : contact@identiguinee.gn
- Téléphone : +224 622 12 34 56

Cordialement,  
L'équipe IdentiGuinée  
Ministère de l'Intérieur - République de Guinée
@endcomponent
