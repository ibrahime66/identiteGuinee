<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentRequestValidated extends Notification implements ShouldQueue
{
    use Queueable;

    public $documentRequest;
    public $document;

    public function __construct($documentRequest, $document = null)
    {
        $this->documentRequest = $documentRequest;
        $this->document = $document;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Votre demande a été validée - IdentiGuinée')
            ->greeting('Félicitations ' . $notifiable->name . ',')
            ->line('Votre demande de ' . $this->documentRequest->document_type_label . ' a été validée !')
            ->line('Référence : **' . $this->documentRequest->reference . '**')
            ->line('Votre document est maintenant disponible dans votre espace citoyen.');

        if ($this->document) {
            $mail->line('Date d\'émission : ' . $this->document->issue_date->format('d/m/Y'))
                 ->line('Date d\'expiration : ' . $this->document->expiry_date->format('d/m/Y'));
        }

        return $mail->action('Télécharger mon document', route('citizen.documents'))
                   ->line('Nous vous remercions de votre confiance dans nos services.')
                   ->salutation('Cordialement,')
                   ->salutation('L\'équipe IdentiGuinée');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Demande validée',
            'message' => 'Votre demande de ' . $this->documentRequest->document_type_label . ' a été validée.',
            'reference' => $this->documentRequest->reference,
            'document_type' => $this->documentRequest->document_type,
            'status' => 'validée',
        ];
    }
}
