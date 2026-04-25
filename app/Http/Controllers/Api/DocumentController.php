<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\DocumentRequest;
use App\Jobs\GenerateDocument;
use App\Jobs\SendDocumentRequestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $requests = $user->documentRequests()
            ->with(['document'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    public function store(StoreDocumentRequest $request)
    {
        $user = Auth::user();

        $documentRequest = DocumentRequest::create([
            'reference' => $this->generateReference($request->document_type),
            'user_id' => $user->id,
            'document_type' => $request->document_type,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'address' => $request->address,
            'phone' => $request->phone,
            'priority' => $request->priority ?? 'normal',
            'notes' => $request->notes,
        ]);

        // Send confirmation email
        dispatch(new SendDocumentRequestEmail($documentRequest));

        return response()->json([
            'success' => true,
            'message' => 'Demande soumise avec succès',
            'data' => $documentRequest->load('user')
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $request = DocumentRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['document', 'validatedBy', 'rejectedBy'])
            ->first();

        if (!$request) {
            return response()->json([
                'success' => false,
                'message' => 'Demande non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $documentRequest = DocumentRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$documentRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Demande non trouvée'
            ], 404);
        }

        if ($documentRequest->isProcessed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette demande ne peut plus être modifiée'
            ], 422);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'birth_date' => 'sometimes|required|date|before:today',
            'birth_place' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:500',
            'phone' => 'sometimes|required|string|max:20|regex:/^[+]?[0-9\s\-\(\)]+$/',
        ]);

        $documentRequest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Demande mise à jour avec succès',
            'data' => $documentRequest
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $documentRequest = DocumentRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$documentRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Demande non trouvée'
            ], 404);
        }

        if ($documentRequest->isProcessed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette demande ne peut plus être supprimée'
            ], 422);
        }

        $documentRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Demande supprimée avec succès'
        ]);
    }

    public function documents(Request $request)
    {
        $user = Auth::user();
        $documents = $user->documents()
            ->valid()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function showDocument($id)
    {
        $user = Auth::user();
        $document = Document::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['request'])
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $document
        ]);
    }

    public function download($id)
    {
        $user = Auth::user();
        $document = Document::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document non trouvé'
            ], 404);
        }

        // In a real implementation, this would return the actual file
        return response()->json([
            'success' => true,
            'message' => 'URL de téléchargement générée',
            'data' => [
                'download_url' => url("/api/v1/documents/{$id}/file"),
                'expires_at' => now()->addMinutes(30)->toISOString(),
            ]
        ]);
    }

    public function stats(Request $request)
    {
        $stats = [
            'total_requests' => DocumentRequest::count(),
            'pending_requests' => DocumentRequest::pending()->count(),
            'validated_requests' => DocumentRequest::validated()->count(),
            'rejected_requests' => DocumentRequest::rejected()->count(),
            'total_documents' => Document::count(),
            'valid_documents' => Document::valid()->count(),
            'today_requests' => DocumentRequest::whereDate('created_at', today())->count(),
            'this_month_requests' => DocumentRequest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function pending(Request $request)
    {
        $requests = DocumentRequest::pending()
            ->with(['user'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    public function validateDocument(Request $request, $id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);

        if (!$documentRequest->canBeValidated()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette demande ne peut pas être validée'
            ], 422);
        }

        dispatch(new GenerateDocument($documentRequest));

        return response()->json([
            'success' => true,
            'message' => 'Demande mise en validation'
        ]);
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $documentRequest = DocumentRequest::findOrFail($id);

        if (!$documentRequest->canBeRejected()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette demande ne peut pas être rejetée'
            ], 422);
        }

        $documentRequest->update([
            'status' => 'rejetée',
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $validated['reason'],
        ]);

        // Send rejection email
        dispatch(new \App\Jobs\SendDocumentRejectionEmail($documentRequest));

        return response()->json([
            'success' => true,
            'message' => 'Demande rejetée avec succès'
        ]);
    }

    private function generateReference($documentType)
    {
        $prefix = strtoupper($documentType);
        $year = date('Y');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$random}";
    }
}
