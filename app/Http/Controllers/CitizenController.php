<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use Dompdf\Dompdf;

class CitizenController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()?->role !== 'citizen') {
                return redirect()->route('login');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $citizenId = Auth::id();
        $requests = DocumentRequest::where('user_id', $citizenId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'reference' => $request->reference,
                    'document_type' => $this->getDocumentTypeLabel($request->document_type),
                    'status' => $request->status,
                    'created_at' => $request->created_at->format('d/m/Y H:i')
                ];
            });

        // Compter les documents disponibles (valides)
        $documentsCount = Document::where('user_id', $citizenId)
            ->where('is_valid', true)
            ->count();

        return view('citizen.dashboard', compact('requests', 'documentsCount'));
    }

    public function createRequest()
    {
        return view('citizen.request');
    }

    public function storeRequest(Request $request)
    {
        $baseRules = [
            'document_type' => 'required|in:cni,passeport,permis,extrait',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20'
        ];

        $additionalRules = [];
        
        // Règles spécifiques selon le type de document
        switch ($request->document_type) {
            case 'extrait':
                $additionalRules = [
                    'father_name' => 'required|string|max:255',
                    'mother_name' => 'required|string|max:255',
                    'father_profession' => 'nullable|string|max:255',
                    'mother_profession' => 'nullable|string|max:255',
                    'declaration_date' => 'nullable|date',
                    'declaration_place' => 'nullable|string|max:255'
                ];
                break;
                
            case 'cni':
                $additionalRules = [
                    'profession' => 'required|string|max:255',
                    'height' => 'nullable|integer|min:100|max:250',
                    'distinguishing_marks' => 'nullable|string|max:500',
                    'has_previous_cni' => 'required|string|in:first,renewal,duplicate',
                    'previous_cni' => 'required_if:has_previous_cni,renewal,duplicate|nullable|string|max:50'
                ];
                break;
                
            case 'passeport':
                $additionalRules = [
                    'profession_passport' => 'required|string|max:255',
                    'travel_purpose' => 'required|string|max:50',
                    'destination_countries' => 'nullable|string|max:500',
                    'has_previous_passport' => 'required|string|in:first,renewal,duplicate',
                    'previous_passport' => 'required_if:has_previous_passport,renewal,duplicate|nullable|string|max:50'
                ];
                break;
                
            case 'permis':
                $additionalRules = [
                    'license_category' => 'required|string|max:10',
                    'driving_experience' => 'nullable|integer|min:0|max:50',
                    'license_issue_country' => 'nullable|string|max:100',
                    'has_previous_license' => 'required|string|in:first,renewal,duplicate,exchange',
                    'previous_license' => 'required_if:has_previous_license,renewal,duplicate,exchange|nullable|string|max:50'
                ];
                break;
        }

        $request->validate(array_merge($baseRules, $additionalRules));

        $citizenId = Auth::id();
        $documentType = $request->document_type;
        
        // Generate unique reference
        $reference = $this->generateReference($documentType);

        // Préparer les données supplémentaires
        $additionalData = [];
        
        switch ($request->document_type) {
            case 'extrait':
                $additionalData = [
                    'notes' => json_encode([
                        'father_name' => $request->father_name,
                        'mother_name' => $request->mother_name,
                        'father_profession' => $request->father_profession,
                        'mother_profession' => $request->mother_profession,
                        'declaration_date' => $request->declaration_date,
                        'declaration_place' => $request->declaration_place
                    ])
                ];
                break;
                
            case 'cni':
                $additionalData = [
                    'notes' => json_encode([
                        'profession' => $request->profession,
                        'height' => $request->height,
                        'distinguishing_marks' => $request->distinguishing_marks,
                        'has_previous_cni' => $request->has_previous_cni,
                        'previous_cni' => $request->previous_cni
                    ])
                ];
                break;
                
            case 'passeport':
                $additionalData = [
                    'notes' => json_encode([
                        'profession' => $request->profession_passport,
                        'travel_purpose' => $request->travel_purpose,
                        'destination_countries' => $request->destination_countries,
                        'has_previous_passport' => $request->has_previous_passport,
                        'previous_passport' => $request->previous_passport
                    ])
                ];
                break;
                
            case 'permis':
                $additionalData = [
                    'notes' => json_encode([
                        'license_category' => $request->license_category,
                        'driving_experience' => $request->driving_experience,
                        'license_issue_country' => $request->license_issue_country,
                        'has_previous_license' => $request->has_previous_license,
                        'previous_license' => $request->previous_license
                    ])
                ];
                break;
        }

        // Créer la demande
        $documentRequest = DocumentRequest::create(array_merge([
            'reference' => $reference,
            'user_id' => $citizenId,
            'document_type' => $documentType,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'address' => $request->address,
            'phone' => $request->phone,
            'status' => 'approuvé',
            'priority' => 'normal'
        ], $additionalData));

        // Générer automatiquement le document
        $this->generateDocument($documentRequest);

        return redirect()->route('citizen.dashboard')->with('success', 'Votre document a été généré avec succès');
    }

    private function generateDocument($documentRequest)
    {
        // Créer le document dans la table documents
        $document = Document::create([
            'reference' => $documentRequest->reference,
            'user_id' => $documentRequest->user_id,
            'document_type' => $documentRequest->document_type,
            'holder_name' => $documentRequest->first_name . ' ' . $documentRequest->last_name,
            'birth_date' => $documentRequest->birth_date,
            'birth_place' => $documentRequest->birth_place,
            'issue_date' => now(),
            'expiry_date' => now()->addYears(5), // Valide 5 ans
            'qr_code' => $this->generateQRCode($documentRequest->reference),
            'is_valid' => true,
            'notes' => $documentRequest->notes,
            'request_id' => $documentRequest->id // Ajout de l'ID de la demande
        ]);

        return $document;
    }

    private function generateQRCode($reference)
    {
        // Générer un code QR simple basé sur la référence
        return 'IDG-' . strtoupper($reference) . '-' . date('Y');
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
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'address' => $request->address,
            'profession' => $request->profession,
            'nationality' => $request->nationality
            // Note: cni_number n'est pas modifiable
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

    public function downloadDocument($reference)
    {
        $citizenId = Session::get('citizen_id');
        $document = Document::where('reference', $reference)
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

    public function deleteDocument($reference)
    {
        $citizenId = Session::get('citizen_id');
        
        // D'abord supprimer la demande (car c'est ce qui est affiché dans le dashboard)
        $request = DocumentRequest::where('reference', $reference)
            ->where('user_id', $citizenId)
            ->first();

        if (!$request) {
            \Log::error('Demande non trouvée pour la référence: ' . $reference);
            return back()->with('error', 'Demande non trouvée');
        }

        \Log::info('Demande trouvée: ' . $request->id . ' - ' . $request->reference);

        // Supprimer le document associé s'il existe
        $document = Document::where('reference', $reference)
            ->where('user_id', $citizenId)
            ->first();

        if ($document) {
            \Log::info('Document trouvé et supprimé: ' . $document->id);
            $document->delete();
        } else {
            \Log::info('Aucun document trouvé pour cette référence');
        }

        // Supprimer la demande
        $request->delete();
        \Log::info('Demande supprimée: ' . $reference);

        return redirect()->route('citizen.dashboard')->with('success', 'Document supprimé avec succès');
    }

    private function generateDocumentPDF($document)
    {
        $documentType = $this->getDocumentTypeLabel($document->document_type);
        
        // Récupérer les données de la demande associée
        $request = DocumentRequest::where('reference', $document->reference)->first();
        $additionalData = $request ? json_decode($request->notes, true) : [];
        
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
                </div>';
        
        // Ajouter les champs spécifiques selon le type de document
        if ($document->document_type === 'extrait') {
            $html .= '
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
                </div>';
            
            if ($request) {
                $html .= '
                <div class="field">
                    <span class="label">Nom du père:</span>
                    <span class="value">' . strtoupper($additionalData['father_name'] ?? 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Nom de la mère:</span>
                    <span class="value">' . strtoupper($additionalData['mother_name'] ?? 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Profession du père:</span>
                    <span class="value">' . ucfirst($additionalData['father_profession'] ?? 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Profession de la mère:</span>
                    <span class="value">' . ucfirst($additionalData['mother_profession'] ?? 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Date de déclaration:</span>
                    <span class="value">' . ($additionalData['declaration_date'] ? date('d/m/Y', strtotime($additionalData['declaration_date'])) : 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Lieu de déclaration:</span>
                    <span class="value">' . ucfirst($additionalData['declaration_place'] ?? 'Non spécifié') . '</span>
                </div>';
            }
            
            $html .= '
            </div>
            
            <div class="section">
                <div class="section-title">Acte de Naissance</div>
                <p style="text-align: center; font-style: italic; margin: 20px 0;">
                    Le soussigné, Officier de l\'État Civil, certifie que l\'acte de naissance ci-dessus
                    a été enregistré dans les registres de la commune et que le présent document
                    est une copie conforme de l\'original.
                </p>
            </div>';
            
        } elseif ($document->document_type === 'cni') {
            $html .= '
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
                </div>';
            
            if ($request) {
                $html .= '
                <div class="field">
                    <span class="label">Profession:</span>
                    <span class="value">' . ucfirst($additionalData['profession'] ?? 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Taille:</span>
                    <span class="value">' . ($additionalData['height'] ? $additionalData['height'] . ' cm' : 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Signes particuliers:</span>
                    <span class="value">' . ucfirst($additionalData['distinguishing_marks'] ?? 'Aucun') . '</span>
                </div>';
            }
            
            $html .= '
            </div>
            
            <div class="section">
                <div class="section-title">Caractéristiques de la CNI</div>
                <p style="text-align: center; font-weight: bold; margin: 20px 0;">
                    Carte Nationale d\'Identité biométrique valide sur le territoire guinéen
                </p>
            </div>';
            
        } elseif ($document->document_type === 'passeport') {
            $html .= '
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
                </div>';
            
            if ($request) {
                $html .= '
                <div class="field">
                    <span class="label">Profession:</span>
                    <span class="value">' . ucfirst($additionalData['profession'] ?? 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Motif de voyage:</span>
                    <span class="value">' . ucfirst($additionalData['travel_purpose'] ?? 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Pays de destination:</span>
                    <span class="value">' . ucfirst($additionalData['destination_countries'] ?? 'Non spécifié') . '</span>
                </div>';
            }
            
            $html .= '
            </div>
            
            <div class="section">
                <div class="section-title">Passeport Guinéen</div>
                <p style="text-align: center; font-weight: bold; margin: 20px 0;">
                    Passeport diplomatique valide pour les voyages internationaux
                </p>
            </div>';
            
        } elseif ($document->document_type === 'permis') {
            $html .= '
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
                </div>';
            
            if ($request) {
                $html .= '
                <div class="field">
                    <span class="label">Catégorie:</span>
                    <span class="value">' . strtoupper($additionalData['license_category'] ?? 'Non spécifié') . '</span>
                </div>
                <div class="field">
                    <span class="label">Années d\'expérience:</span>
                    <span class="value">' . ($additionalData['driving_experience'] ?? '0') . ' ans</span>
                </div>
                <div class="field">
                    <span class="label">Pays de délivrance:</span>
                    <span class="value">' . ucfirst($additionalData['license_issue_country'] ?? 'Guinée') . '</span>
                </div>';
            }
            
            $html .= '
            </div>
            
            <div class="section">
                <div class="section-title">Permis de Conduire</div>
                <p style="text-align: center; font-weight: bold; margin: 20px 0;">
                    Permis de conduire valide sur le territoire guinéen
                </p>
            </div>';
        }
        
        $html .= '
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
            'permis' => 'Permis de conduire',
            'extrait' => 'Extrait de naissance'
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
