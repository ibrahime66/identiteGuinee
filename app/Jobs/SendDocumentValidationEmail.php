<?php

namespace App\Jobs;

use App\Models\DocumentRequest;
use App\Models\Document;
use App\Notifications\DocumentRequestValidated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDocumentValidationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentRequest;
    public $document;

    public function __construct(DocumentRequest $documentRequest, Document $document = null)
    {
        $this->documentRequest = $documentRequest;
        $this->document = $document;
    }

    public function handle()
    {
        $user = $this->documentRequest->user;
        $user->notify(new DocumentRequestValidated($this->documentRequest, $this->document));
    }

    public function failed(\Throwable $exception)
    {
        \Log::error('Failed to send document validation email: ' . $exception->getMessage(), [
            'request_id' => $this->documentRequest->id,
            'user_id' => $this->documentRequest->user_id,
        ]);
    }
}
