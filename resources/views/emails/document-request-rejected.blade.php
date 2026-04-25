@component('mail::message')
# Information concernant votre demande

Bonjour {{ $notifiable->name }},

Nous vous informons que votre demande de {{ $documentRequest->document_type_label }} n'a pas pu être validée.

**Référence de votre demande :** {{ $documentRequest->reference }}

**Motif du rejet :**
{{ $documentRequest->rejection_reason }}

**Que faire maintenant ?**

1. **Analysez le motif** : Prenez connaissance de la raison du rejet
2. **Corrigez les informations** : Préparez les documents ou informations manquantes
3. **Soumettez une nouvelle demande** : Créez une nouvelle demande avec les corrections

@component('mail::button', ['url' => route('citizen.request.create')])
Nouvelle demande
@endcomponent

**Conseils pour éviter un nouveau rejet :**
- Vérifiez que tous les documents sont lisibles et valides
- Assurez-vous que les informations correspondent à vos pièces d'identité
- Suivez attentivement les instructions du formulaire
- Contactez-nous si vous avez des doutes

**Besoin d'aide ?**
Notre service client est à votre disposition :
- Email : support@identiguinee.gn
- Téléphone : +224 622 12 34 56
- Horaires : Lundi-Vendredi, 8h-17h

Nous sommes là pour vous accompagner dans vos démarches.

Cordialement,  
L'équipe IdentiGuinée  
Ministère de l'Intérieur - République de Guinée
@endcomponent
