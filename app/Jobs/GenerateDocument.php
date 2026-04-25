<?php

namespace App\Jobs;

use App\Models\DocumentRequest;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class GenerateDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentRequest;

    public function __construct(DocumentRequest $documentRequest)
    {
        $this->documentRequest = $documentRequest;
    }

    public function handle()
    {
        // Generate the document
        $document = Document::create([
            'reference' => $this->generateReference(),
            'user_id' => $this->documentRequest->user_id,
            'request_id' => $this->documentRequest->id,
            'document_type' => $this->documentRequest->document_type,
            'holder_name' => $this->documentRequest->first_name . ' ' . $this->documentRequest->last_name,
            'birth_date' => $this->documentRequest->birth_date,
            'birth_place' => $this->documentRequest->birth_place,
            'issue_date' => now(),
            'expiry_date' => $this->calculateExpiryDate(),
            'qr_code' => $this->generateQrCode(),
            'is_valid' => true,
        ]);

        // Update the request status
        $this->documentRequest->update([
            'status' => 'validée',
            'validated_at' => now(),
            'validated_by' => $this->documentRequest->validated_by,
        ]);

        // Send notification email
        dispatch(new SendDocumentValidationEmail($this->documentRequest, $document));

        \Log::info('Document generated successfully', [
            'document_id' => $document->id,
            'request_id' => $this->documentRequest->id,
            'user_id' => $this->documentRequest->user_id,
        ]);
    }

    private function generateReference()
    {
        $prefix = strtoupper($this->documentRequest->document_type);
        $year = date('Y');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$random}";
    }

    private function generateQrCode()
    {
        return 'DOC-' . strtoupper($this->documentRequest->document_type) . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function calculateExpiryDate()
    {
        $years = match($this->documentRequest->document_type) {
            'cni' => 10,
            'passeport' => 5,
            'permis' => 5,
            default => 10,
        };

        return now()->addYears($years);
    }

    public function failed(\Throwable $exception)
    {
        \Log::error('Failed to generate document: ' . $exception->getMessage(), [
            'request_id' => $this->documentRequest->id,
            'user_id' => $this->documentRequest->user_id,
        ]);

        // Update request status to indicate failure
        $this->documentRequest->update([
            'notes' => $this->documentRequest->notes . "\nErreur lors de la génération: " . $exception->getMessage(),
        ]);
    }
}
