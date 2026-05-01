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
                return redirect()->route('admin.login');
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
            'reason' => 'required|string|max:500'
        ]);

        $adminId = Session::get('admin_id');
        $documentRequest = DocumentRequest::findOrFail($id);
        
        $documentRequest->update([
            'status' => 'rejetée',
            'rejection_reason' => $request->reason,
            'rejected_at' => now(),
            'rejected_by' => $adminId
        ]);

        return redirect()->route('admin.requests')->with('success', 'La demande a été rejetée avec la raison spécifiée');
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
}
