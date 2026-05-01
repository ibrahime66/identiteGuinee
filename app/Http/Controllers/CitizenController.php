<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\DocumentRequest;
use App\Models\Document;
use App\Models\User;

class CitizenController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Session::get('citizen_authenticated')) {
                return redirect()->route('citizen.login');
            }
            return $next($request);
        })->except(['showCitizenLogin', 'citizenLogin', 'showCitizenRegister', 'citizenRegister']);
    }

    public function dashboard()
    {
        $citizenId = Session::get('citizen_id');
        $requests = DocumentRequest::where('user_id', $citizenId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'type' => $this->getDocumentTypeLabel($request->document_type),
                    'status' => $request->status,
                    'date' => $request->created_at->format('Y-m-d'),
                    'reference' => $request->reference
                ];
            });

        return view('citizen.dashboard', compact('requests'));
    }

    public function createRequest()
    {
        return view('citizen.request');
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:cni,passeport,permis',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20'
        ]);

        $citizenId = Session::get('citizen_id');
        $documentType = $request->document_type;
        
        // Generate unique reference
        $reference = $this->generateReference($documentType);

        DocumentRequest::create([
            'reference' => $reference,
            'user_id' => $citizenId,
            'document_type' => $documentType,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'address' => $request->address,
            'phone' => $request->phone,
            'status' => 'en cours',
            'priority' => 'normal'
        ]);

        return redirect()->route('citizen.dashboard')->with('success', 'Votre demande a été soumise avec succès');
    }

    public function documents()
    {
        $citizenId = Session::get('citizen_id');
        $documents = Document::where('user_id', $citizenId)
            ->where('is_valid', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($document) {
                return [
                    'id' => $document->id,
                    'type' => $this->getDocumentTypeLabel($document->document_type),
                    'reference' => $document->reference,
                    'issue_date' => $document->issue_date->format('Y-m-d'),
                    'expiry_date' => $document->expiry_date->format('Y-m-d'),
                    'status' => $document->is_valid ? 'valide' : 'invalide'
                ];
            });

        return view('citizen.documents', compact('documents'));
    }

    public function downloadDocument($id)
    {
        $citizenId = Session::get('citizen_id');
        $document = Document::where('id', $id)
            ->where('user_id', $citizenId)
            ->where('is_valid', true)
            ->first();

        if (!$document) {
            return back()->with('error', 'Document non trouvé ou non valide');
        }

        // Generate PDF content
        $pdfContent = $this->generateDocumentPDF($document);
        
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $document->reference . '.pdf"');
    }

    private function generateDocumentPDF($document)
    {
        // Simple HTML to PDF conversion
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; border-bottom: 2px solid #0066cc; padding-bottom: 20px; margin-bottom: 30px; }
                .title { color: #0066cc; font-size: 24px; font-weight: bold; }
                .content { margin: 20px 0; }
                .field { margin: 10px 0; }
                .label { font-weight: bold; display: inline-block; width: 150px; }
                .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #666; }
                .valid { color: green; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="title">RÉPUBLIQUE DE GUINÉE</div>
                <div>' . $this->getDocumentTypeLabel($document->document_type) . '</div>
            </div>
            
            <div class="content">
                <div class="field">
                    <span class="label">Référence:</span> ' . $document->reference . '
                </div>
                <div class="field">
                    <span class="label">Titulaire:</span> ' . $document->holder_name . '
                </div>
                <div class="field">
                    <span class="label">Date de naissance:</span> ' . $document->birth_date . '
                </div>
                <div class="field">
                    <span class="label">Lieu de naissance:</span> ' . $document->birth_place . '
                </div>
                <div class="field">
                    <span class="label">Date d\'émission:</span> ' . $document->issue_date->format('d/m/Y') . '
                </div>
                <div class="field">
                    <span class="label">Date d\'expiration:</span> ' . $document->expiry_date->format('d/m/Y') . '
                </div>
                <div class="field">
                    <span class="label">Statut:</span> <span class="valid">VALIDE</span>
                </div>
                <div class="field">
                    <span class="label">Code QR:</span> ' . $document->qr_code . '
                </div>
            </div>
            
            <div class="footer">
                <p>Ce document est authentique et a été généré électroniquement</p>
                <p>Ministère de l\'Intérieur - République de Guinée</p>
                <p>Généré le: ' . now()->format('d/m/Y H:i') . '</p>
            </div>
        </body>
        </html>';

        // For simplicity, we'll use a basic approach
        // In production, you might want to use a proper PDF library like DomPDF
        return $html;
    }

    private function getDocumentTypeLabel($type)
    {
        $labels = [
            'cni' => 'Carte Nationale d\'Identité',
            'passeport' => 'Passeport',
            'permis' => 'Permis de conduire'
        ];

        return $labels[$type] ?? $type;
    }

    private function generateReference($type)
    {
        $prefix = strtoupper($type);
        $year = date('Y');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$random}";
    }
}
