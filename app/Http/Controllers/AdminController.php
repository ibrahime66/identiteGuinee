<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
            'total_requests' => 1247,
            'pending_requests' => 89,
            'validated_requests' => 1056,
            'rejected_requests' => 102,
            'today_requests' => 23
        ];

        $recentRequests = [
            [
                'id' => 1,
                'reference' => 'CNI-2024-001234',
                'citizen_name' => 'Mamadou Diallo',
                'document_type' => 'Carte Nationale d\'Identité',
                'status' => 'en cours',
                'date' => '2024-03-15'
            ],
            [
                'id' => 2,
                'reference' => 'PAS-2024-000567',
                'citizen_name' => 'Aïssatou Bah',
                'document_type' => 'Passeport',
                'status' => 'en cours',
                'date' => '2024-03-15'
            ],
            [
                'id' => 3,
                'reference' => 'PER-2024-000890',
                'citizen_name' => 'Ousmane Condé',
                'document_type' => 'Permis de conduire',
                'status' => 'en cours',
                'date' => '2024-03-14'
            ]
        ];

        return view('admin.dashboard', compact('stats', 'recentRequests'));
    }

    public function requests()
    {
        $requests = [
            [
                'id' => 1,
                'reference' => 'CNI-2024-001234',
                'citizen_name' => 'Mamadou Diallo',
                'citizen_email' => 'mamadou.diallo@email.com',
                'document_type' => 'Carte Nationale d\'Identité',
                'status' => 'en cours',
                'date' => '2024-03-15',
                'priority' => 'normal'
            ],
            [
                'id' => 2,
                'reference' => 'PAS-2024-000567',
                'citizen_name' => 'Aïssatou Bah',
                'citizen_email' => 'aissatou.bah@email.com',
                'document_type' => 'Passeport',
                'status' => 'en cours',
                'date' => '2024-03-15',
                'priority' => 'urgent'
            ],
            [
                'id' => 3,
                'reference' => 'PER-2024-000890',
                'citizen_name' => 'Ousmane Condé',
                'citizen_email' => 'ousmane.conde@email.com',
                'document_type' => 'Permis de conduire',
                'status' => 'en cours',
                'date' => '2024-03-14',
                'priority' => 'normal'
            ],
            [
                'id' => 4,
                'reference' => 'CNI-2024-001235',
                'citizen_name' => 'Fatoumata Touré',
                'citizen_email' => 'fatoumata.toure@email.com',
                'document_type' => 'Carte Nationale d\'Identité',
                'status' => 'validée',
                'date' => '2024-03-13',
                'priority' => 'normal'
            ]
        ];

        return view('admin.requests', compact('requests'));
    }

    public function showRequest($id)
    {
        $request = [
            'id' => $id,
            'reference' => 'CNI-2024-001234',
            'citizen_name' => 'Mamadou Diallo',
            'citizen_email' => 'mamadou.diallo@email.com',
            'citizen_phone' => '+224 622 12 34 56',
            'document_type' => 'Carte Nationale d\'Identité',
            'status' => 'en cours',
            'date' => '2024-03-15',
            'first_name' => 'Mamadou',
            'last_name' => 'Diallo',
            'birth_date' => '1990-05-15',
            'birth_place' => 'Conakry',
            'address' => 'Rue du Commerce, Dixinn, Conakry',
            'profession' => 'Comptable',
            'nationality' => 'Guinéenne',
            'notes' => 'Demande complète avec tous les documents requis.'
        ];

        return view('admin.request-detail', compact('request'));
    }

    public function validateRequest(Request $request, $id)
    {
        return redirect()->route('admin.requests')->with('success', 'La demande a été validée avec succès');
    }

    public function rejectRequest(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        return redirect()->route('admin.requests')->with('success', 'La demande a été rejetée avec la raison spécifiée');
    }
}
