<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use Dompdf\Dompdf;

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

    public function profile()
    {
        $citizenId = Session::get('citizen_id');
        $citizen = User::find($citizenId);
        
        if (!$citizen) {
            return redirect()->route('citizen.dashboard')->with('error', 'Utilisateur non trouvé');
        }

        return view('citizen.profile', compact('citizen'));
    }

    public function editProfile()
    {
        $citizenId = Session::get('citizen_id');
        $citizen = User::find($citizenId);
        
        if (!$citizen) {
            return redirect()->route('citizen.dashboard')->with('error', 'Utilisateur non trouvé');
        }

        return view('citizen.edit-profile', compact('citizen'));
    }

    public function updateProfile(Request $request)
    {
        $citizenId = Session::get('citizen_id');
        $citizen = User::find($citizenId);
        
        if (!$citizen) {
            return redirect()->route('citizen.dashboard')->with('error', 'Utilisateur non trouvé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'cni_number' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'profession' => 'nullable|string|max:100',
            'nationality' => 'required|string|max:50'
        ]);

        $citizen->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'cni_number' => $request->cni_number,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'address' => $request->address,
            'profession' => $request->profession,
            'nationality' => $request->nationality
        ]);

        return redirect()->route('citizen.profile')->with('success', 'Profil mis à jour avec succès');
    }

    public function showChangePassword()
    {
        return view('citizen.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $citizenId = Session::get('citizen_id');
        $citizen = User::find($citizenId);
        
        if (!$citizen) {
            return back()->with('error', 'Utilisateur non trouvé');
        }

        // Vérifier le mot de passe actuel
        if (!password_verify($request->current_password, $citizen->password)) {
            return back()->with('error', 'Le mot de passe actuel est incorrect');
        }

        // Mettre à jour le mot de passe
        $citizen->update([
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('citizen.profile')->with('success', 'Mot de passe changé avec succès');
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
        $html = $this->generateDocumentPDF($document);
        
        // Create PDF using DomPDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->stream($document->reference . '.pdf', ['Attachment' => true]);
    }

    private function generateDocumentPDF($document)
    {
        $documentType = $this->getDocumentTypeLabel($document->document_type);
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            margin: 0; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .document {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 2px solid #0066cc;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header { 
            text-align: center; 
            border-bottom: 3px solid #0066cc; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
        }
        .title { 
            color: #0066cc; 
            font-size: 28px; 
            font-weight: bold; 
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .subtitle { 
            font-size: 18px; 
            color: #333;
            font-weight: bold;
        }
        .content { 
            margin: 30px 0; 
            line-height: 1.6;
        }
        .section {
            margin: 25px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #0066cc;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .field { 
            margin: 12px 0; 
            display: flex;
            align-items: center;
        }
        .label { 
            font-weight: bold; 
            min-width: 180px; 
            color: #333;
        }
        .value {
            color: #000;
            font-size: 14px;
        }
        .footer { 
            text-align: center; 
            margin-top: 40px; 
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px; 
            color: #666; 
        }
        .valid { 
            color: #2e7d32; 
            font-weight: bold; 
            font-size: 16px;
            background: #e8f5e8;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f0f0f0;
            border: 1px dashed #ccc;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 102, 204, 0.1);
            font-weight: bold;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">IDENTIGUINÉE</div>
    
    <div class="document">
        <div class="header">
            <div class="title">République de Guinée</div>
            <div class="subtitle">' . $documentType . '</div>
        </div>
        
        <div class="content">
            <div class="section">
                <div class="section-title">Informations du Document</div>
                <div class="field">
                    <span class="label">Référence:</span>
                    <span class="value">' . $document->reference . '</span>
                </div>
                <div class="field">
                    <span class="label">Date d\'émission:</span>
                    <span class="value">' . $document->issue_date->format('d/m/Y') . '</span>
                </div>
                <div class="field">
                    <span class="label">Date d\'expiration:</span>
                    <span class="value">' . $document->expiry_date->format('d/m/Y') . '</span>
                </div>
                <div class="field">
                    <span class="label">Statut:</span>
                    <span class="valid">VALIDE</span>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Informations du Titulaire</div>
                <div class="field">
                    <span class="label">Nom complet:</span>
                    <span class="value">' . strtoupper($document->holder_name) . '</span>
                </div>
                <div class="field">
                    <span class="label">Date de naissance:</span>
                    <span class="value">' . $document->birth_date . '</span>
                </div>
                <div class="field">
                    <span class="label">Lieu de naissance:</span>
                    <span class="value">' . ucfirst($document->birth_place) . '</span>
                </div>
            </div>
            
            <div class="qr-code">
                <div class="section-title">Code de Vérification</div>
                <div style="font-size: 18px; font-weight: bold; color: #0066cc;">
                    ' . $document->qr_code . '
                </div>
                <small>Ce code permet de vérifier l\'authenticité du document</small>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Ministère de l\'Intérieur</strong></p>
            <p>République de Guinée</p>
            <p>Document généré électroniquement le: ' . now()->format('d/m/Y à H:i') . '</p>
            <p><em>Ce document est authentique et a une valeur légale</em></p>
        </div>
    </div>
</body>
</html>';

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
