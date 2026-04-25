<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentRequestRejected extends Notification implements ShouldQueue
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
            ->subject('Information concernant votre demande - IdentiGuinée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous vous informons que votre demande de ' . $this->documentRequest->document_type_label . ' n\'a pas pu être validée.')
            ->line('Référence de votre demande : **' . $this->documentRequest->reference . '**')
            ->line('Motif du rejet :')
            ->line($this->documentRequest->rejection_reason)
            ->line('Vous pouvez soumettre une nouvelle demande en corrigeant les points mentionnés.')
            ->action('Nouvelle demande', route('citizen.request.create'))
            ->line('Pour toute question ou pour obtenir plus d\'informations, n\'hésitez pas à nous contacter.')
            ->salutation('Cordialement,')
            ->salutation('L\'équipe IdentiGuinée');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Demande rejetée',
            'message' => 'Votre demande de ' . $this->documentRequest->document_type_label . ' a été rejetée.',
            'reference' => $this->documentRequest->reference,
            'document_type' => $this->documentRequest->document_type,
            'status' => 'rejetée',
            'rejection_reason' => $this->documentRequest->rejection_reason,
        ];
    }
}
