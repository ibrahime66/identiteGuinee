<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public $documentRequest;

    public function __construct($documentRequest)
    {
        $this->documentRequest = $documentRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Confirmation de votre demande de document - IdentiGuinée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous vous confirmons la réception de votre demande de ' . $this->documentRequest->document_type_label . '.')
            ->line('Référence de votre demande : **' . $this->documentRequest->reference . '**')
            ->line('Votre demande est maintenant en cours de traitement par nos services.')
            ->line('Vous pouvez suivre l\'évolution de votre demande en vous connectant à votre espace citoyen.')
            ->action('Suivre ma demande', route('citizen.dashboard'))
            ->line('Le délai de traitement moyen est de 3 à 5 jours ouvrés.')
            ->line('Pour toute question, n\'hésitez pas à nous contacter.')
            ->salutation('Cordialement,')
            ->salutation('L\'équipe IdentiGuinée');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Nouvelle demande de document',
            'message' => 'Votre demande de ' . $this->documentRequest->document_type_label . ' a été reçue.',
            'reference' => $this->documentRequest->reference,
            'document_type' => $this->documentRequest->document_type,
            'status' => $this->documentRequest->status,
        ];
    }
}
