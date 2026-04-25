<?php

namespace App\Jobs;

use App\Models\DocumentRequest;
use App\Notifications\DocumentRequestRejected;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDocumentRejectionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentRequest;

    public function __construct(DocumentRequest $documentRequest)
    {
        $this->documentRequest = $documentRequest;
    }

    public function handle()
    {
        $user = $this->documentRequest->user;
        $user->notify(new DocumentRequestRejected($this->documentRequest));
    }

    public function failed(\Throwable $exception)
    {
        \Log::error('Failed to send document rejection email: ' . $exception->getMessage(), [
            'request_id' => $this->documentRequest->id,
            'user_id' => $this->documentRequest->user_id,
        ]);
    }
}
