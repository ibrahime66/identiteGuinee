<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'document_code' => 'required|string|max:50'
        ]);

        $document = Document::where('qr_code', $validated['document_code'])
            ->orWhere('reference', $validated['document_code'])
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document non trouvé',
                'data' => [
                    'valid' => false,
                    'reason' => 'Document non trouvé dans notre base de données'
                ]
            ], 404);
        }

        $verificationData = [
            'valid' => $document->isValid(),
            'document_type' => $document->document_type,
            'document_type_label' => $document->document_type_label,
            'holder_name' => $document->holder_name,
            'birth_date' => $document->birth_date->format('d/m/Y'),
            'birth_place' => $document->birth_place,
            'issue_date' => $document->issue_date->format('d/m/Y'),
            'expiry_date' => $document->expiry_date->format('d/m/Y'),
            'reference' => $document->reference,
            'qr_code' => $document->qr_code,
            'verified_at' => now()->toISOString(),
            'verification_id' => uniqid('VER_'),
        ];

        if ($document->isRevoked()) {
            $verificationData['status'] = 'revoked';
            $verificationData['reason'] = $document->revocation_reason;
        } elseif ($document->isExpired()) {
            $verificationData['status'] = 'expired';
        } else {
            $verificationData['status'] = 'valid';
        }

        return response()->json([
            'success' => true,
            'message' => $document->isValid() ? 'Document valide' : 'Document invalide',
            'data' => $verificationData
        ]);
    }

    public function verifyByCode($code)
    {
        $document = Document::where('qr_code', $code)
            ->orWhere('reference', $code)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document non trouvé',
                'data' => [
                    'valid' => false,
                    'reason' => 'Document non trouvé dans notre base de données'
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document trouvé',
            'data' => [
                'valid' => $document->isValid(),
                'document_type' => $document->document_type,
                'holder_name' => $document->holder_name,
                'reference' => $document->reference,
            ]
        ]);
    }
}
