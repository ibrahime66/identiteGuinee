<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $validDocuments = [
            'CNI-2024-001234' => [
                'valid' => true,
                'type' => 'Carte Nationale d\'Identité',
                'holder_name' => 'Mamadou Diallo',
                'issue_date' => '2024-01-20',
                'expiry_date' => '2034-01-20',
                'birth_date' => '1990-05-15',
                'birth_place' => 'Conakry'
            ],
            'PAS-2024-000567' => [
                'valid' => true,
                'type' => 'Passeport',
                'holder_name' => 'Aïssatou Bah',
                'issue_date' => '2024-02-15',
                'expiry_date' => '2029-02-15',
                'birth_date' => '1985-08-22',
                'birth_place' => 'Kankan'
            ],
            'PER-2024-000890' => [
                'valid' => true,
                'type' => 'Permis de conduire',
                'holder_name' => 'Ousmane Condé',
                'issue_date' => '2024-01-10',
                'expiry_date' => '2029-01-10',
                'birth_date' => '1992-12-03',
                'birth_place' => 'Labé'
            ]
        ];

        $document = $validDocuments[$documentCode] ?? ['valid' => false];

        return view('verifier.result', compact('document', 'documentCode'));
    }
}
