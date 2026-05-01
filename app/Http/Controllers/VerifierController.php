<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class VerifierController extends Controller
{
    public function index()
    {
        return view('verifier.index');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'document_code' => 'required|string|max:50'
        ]);

        $documentCode = $request->document_code;

        // Search for document in database
        $document = Document::where('qr_code', $documentCode)
            ->where('is_valid', true)
            ->whereNull('revoked_at')
            ->where('expiry_date', '>', now())
            ->with('user')
            ->first();

        if ($document) {
            $documentData = [
                'valid' => true,
                'type' => $this->getDocumentTypeLabel($document->document_type),
                'holder_name' => $document->holder_name,
                'issue_date' => $document->issue_date->format('d/m/Y'),
                'expiry_date' => $document->expiry_date->format('d/m/Y'),
                'birth_date' => $document->birth_date,
                'birth_place' => $document->birth_place,
                'reference' => $document->reference,
                'user_email' => $document->user->email,
                'verification_date' => now()->format('d/m/Y H:i:s')
            ];
        } else {
            $documentData = [
                'valid' => false,
                'verification_date' => now()->format('d/m/Y H:i:s')
            ];
        }

        return view('verifier.result', [
            'document' => $documentData, 
            'documentCode' => $documentCode
        ]);
    }

    private function getDocumentTypeLabel($type)
    {
        $labels = [
            'cni' => 'Carte Nationale d\'Identité',
            'passeport' => 'Passeport',
            'permis' => 'Permis de conduire',
            'extrait' => 'Extrait de naissance'
        ];

        return $labels[$type] ?? $type;
    }
}
