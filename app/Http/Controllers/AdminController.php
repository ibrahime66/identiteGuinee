<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\DocumentRequest;
use App\Models\Document;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Session::get('admin_authenticated')) {
                return redirect()->route('login');
            }
            return $next($request);
        })->except(['showAdminLogin', 'adminLogin']);
    }

    public function dashboard()
    {
        $stats = [
            'total_requests' => DocumentRequest::count(),
            'pending_requests' => DocumentRequest::where('status', 'en cours')->count(),
            'validated_requests' => DocumentRequest::where('status', 'validée')->count(),
            'rejected_requests' => DocumentRequest::where('status', 'rejetée')->count(),
            'today_requests' => DocumentRequest::whereDate('created_at', today())->count()
        ];

        $recentRequests = DocumentRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'reference' => $request->reference,
                    'citizen_name' => $request->user->name,
                    'document_type' => $this->getDocumentTypeLabel($request->document_type),
                    'status' => $request->status,
                    'date' => $request->created_at->format('Y-m-d')
                ];
            });

        return view('admin.dashboard', compact('stats', 'recentRequests'));
    }

    public function requests()
    {
        $status = request('status');
        $query = DocumentRequest::with('user')->orderBy('created_at', 'desc');
        
        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->get()->map(function ($request) {
            return [
                'id' => $request->id,
                'reference' => $request->reference,
                'citizen_name' => $request->user->name,
                'citizen_email' => $request->user->email,
                'citizen_phone' => $request->user->phone,
                'document_type' => $this->getDocumentTypeLabel($request->document_type),
                'status' => $request->status,
                'date' => $request->created_at->format('Y-m-d'),
                'priority' => $request->priority
            ];
        });

        return view('admin.requests', compact('requests'));
    }

    public function showRequest($id)
    {
        $documentRequest = DocumentRequest::with('user')->findOrFail($id);
        
        $request = [
            'id' => $documentRequest->id,
            'reference' => $documentRequest->reference,
            'citizen_name' => $documentRequest->user->name,
            'citizen_email' => $documentRequest->user->email,
            'citizen_phone' => $documentRequest->user->phone,
            'document_type' => $this->getDocumentTypeLabel($documentRequest->document_type),
            'status' => $documentRequest->status,
            'date' => $documentRequest->created_at->format('Y-m-d'),
            'first_name' => $documentRequest->first_name,
            'last_name' => $documentRequest->last_name,
            'birth_date' => $documentRequest->birth_date,
            'birth_place' => $documentRequest->birth_place,
            'address' => $documentRequest->address,
            'profession' => $documentRequest->user->profession,
            'nationality' => $documentRequest->user->nationality,
            'notes' => $documentRequest->notes,
            'priority' => $documentRequest->priority,
            'rejection_reason' => $documentRequest->rejection_reason
        ];

        return view('admin.request-detail', compact('request'));
    }

    public function validateRequest(Request $request, $id)
    {
        $adminId = Session::get('admin_id');
        $documentRequest = DocumentRequest::findOrFail($id);
        
        $documentRequest->update([
            'status' => 'validée',
            'validated_at' => now(),
            'validated_by' => $adminId
        ]);

        // Create the document
        Document::create([
            'reference' => $documentRequest->reference,
            'user_id' => $documentRequest->user_id,
            'request_id' => $documentRequest->id,
            'document_type' => $documentRequest->document_type,
            'holder_name' => $documentRequest->first_name . ' ' . $documentRequest->last_name,
            'birth_date' => $documentRequest->birth_date,
            'birth_place' => $documentRequest->birth_place,
            'issue_date' => now(),
            'expiry_date' => now()->addYears(10),
            'qr_code' => $documentRequest->reference,
            'is_valid' => true
        ]);

        return redirect()->route('admin.requests')->with('success', 'La demande a été validée avec succès');
    }

    public function rejectRequest(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $documentRequest = DocumentRequest::find($id);
        if (!$documentRequest) {
            return back()->with('error', 'Demande non trouvée');
        }

        $documentRequest->update([
            'status' => 'rejetée',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => Session::get('admin_id')
        ]);

        return redirect()->route('admin.requests')->with('success', 'Demande rejetée avec succès');
    }

    public function reports()
    {
        // Statistiques pour les rapports
        $stats = [
            'total_requests' => DocumentRequest::count(),
            'pending_requests' => DocumentRequest::where('status', 'en cours')->count(),
            'validated_requests' => DocumentRequest::where('status', 'validée')->count(),
            'rejected_requests' => DocumentRequest::where('status', 'rejetée')->count(),
            'total_documents' => Document::count(),
            'valid_documents' => Document::where('is_valid', true)->count(),
            'total_users' => User::where('role', 'citizen')->count(),
        ];

        // Demandes par type de document
        $requestsByType = DocumentRequest::selectRaw('document_type, COUNT(*) as count')
            ->groupBy('document_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->document_type => $item->count];
            });

        // Demandes par mois (6 derniers mois)
        $monthlyStats = DocumentRequest::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports', compact('stats', 'requestsByType', 'monthlyStats'));
    }

    public function settings()
    {
        // Paramètres système dynamiques depuis la configuration
        $settings = [
            'app_name' => config('app.name'),
            'app_version' => '1.0.0',
            'max_requests_per_day' => config('app.max_requests_per_day', 5),
            'auto_validation_enabled' => config('app.auto_validation', false),
            'notification_email' => config('mail.from.address', 'admin@identiguinee.gn'),
            'maintenance_mode' => app()->isDownForMaintenance(),
        ];

        // Informations système
        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'database' => config('database.default'),
            'timezone' => config('app.timezone'),
            'debug_mode' => config('app.debug'),
            'url' => config('app.url'),
        ];

        return view('admin.settings', compact('settings', 'systemInfo'));
    }

    public function clearCache()
    {
        try {
            // Vider tous les caches
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            
            return redirect()->route('admin.settings')->with('success', 'Cache vidé avec succès');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings')->with('error', 'Erreur lors du vidage du cache: ' . $e->getMessage());
        }
    }

    public function exportData()
    {
        try {
            // Exporter les données en CSV
            $users = \App\Models\User::where('role', 'citizen')->get();
            $requests = \App\Models\DocumentRequest::all();
            $documents = \App\Models\Document::all();
            
            $csvData = "=== EXPORT DES DONNEES IDENTIGUINEE ===\n";
            $csvData .= "Date d'export: " . now()->format('d/m/Y H:i:s') . "\n\n";
            
            // Export utilisateurs
            $csvData .= "=== UTILISATEURS ===\n";
            $csvData .= "ID;Nom;Email;Téléphone;Date de création\n";
            foreach ($users as $user) {
                $csvData .= "{$user->id};{$user->name};{$user->email};{$user->phone};{$user->created_at->format('d/m/Y')}\n";
            }
            
            // Export demandes
            $csvData .= "\n=== DEMANDES ===\n";
            $csvData .= "ID;Référence;Type;Statut;Date de création\n";
            foreach ($requests as $request) {
                $csvData .= "{$request->id};{$request->reference};{$request->document_type};{$request->status};{$request->created_at->format('d/m/Y')}\n";
            }
            
            // Export documents
            $csvData .= "\n=== DOCUMENTS ===\n";
            $csvData .= "ID;Référence;Type;Titulaire;Date d'émission;Validité\n";
            foreach ($documents as $document) {
                $validity = $document->is_valid ? 'Valide' : 'Invalide';
                $csvData .= "{$document->id};{$document->reference};{$document->document_type};{$document->holder_name};{$document->issue_date->format('d/m/Y')};{$validity}\n";
            }
            
            $filename = 'export_identiguinee_' . date('Y-m-d_H-i-s') . '.csv';
            
            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.settings')->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    public function backupDatabase()
    {
        try {
            // Créer une sauvegarde simple de la base de données
            $backupData = "=== SAUVEGARDE BASE DE DONNEES IDENTIGUINEE ===\n";
            $backupData .= "Date de sauvegarde: " . now()->format('d/m/Y H:i:s') . "\n";
            $backupData .= "Base de données: " . config('database.default') . "\n\n";
            
            // Statistiques
            $backupData .= "=== STATISTIQUES ===\n";
            $backupData .= "Utilisateurs: " . \App\Models\User::count() . "\n";
            $backupData .= "Demandes: " . \App\Models\DocumentRequest::count() . "\n";
            $backupData .= "Documents: " . \App\Models\Document::count() . "\n\n";
            
            // Données principales
            $backupData .= "=== DONNEES UTILISATEURS ===\n";
            $users = \App\Models\User::all();
            foreach ($users as $user) {
                $backupData .= "User[{$user->id}]: {$user->name} ({$user->email})\n";
            }
            
            $backupData .= "\n=== DONNEES DEMANDES ===\n";
            $requests = \App\Models\DocumentRequest::all();
            foreach ($requests as $request) {
                $backupData .= "Request[{$request->id}]: {$request->reference} - {$request->status}\n";
            }
            
            $backupData .= "\n=== DONNEES DOCUMENTS ===\n";
            $documents = \App\Models\Document::all();
            foreach ($documents as $document) {
                $validity = $document->is_valid ? 'Valide' : 'Invalide';
                $backupData .= "Document[{$document->id}]: {$document->reference} - {$validity}\n";
            }
            
            $filename = 'backup_identiguinee_' . date('Y-m-d_H-i-s') . '.txt';
            
            // Sauvegarder le fichier
            $backupPath = storage_path('backups/' . $filename);
            if (!is_dir(storage_path('backups'))) {
                mkdir(storage_path('backups'), 0755, true);
            }
            file_put_contents($backupPath, $backupData);
            
            return redirect()->route('admin.settings')->with('success', 'Base de données sauvegardée avec succès dans ' . $filename);
            
        } catch (\Exception $e) {
            return redirect()->route('admin.settings')->with('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
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

    public static function getDocumentTypeLabelStatic($type)
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
