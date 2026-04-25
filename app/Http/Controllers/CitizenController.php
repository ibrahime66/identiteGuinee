<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        $requests = [
            [
                'id' => 1,
                'type' => 'Carte Nationale d\'Identité',
                'status' => 'validée',
                'date' => '2024-01-15',
                'reference' => 'CNI-2024-001234'
            ],
            [
                'id' => 2,
                'type' => 'Passeport',
                'status' => 'en cours',
                'date' => '2024-02-20',
                'reference' => 'PAS-2024-000567'
            ],
            [
                'id' => 3,
                'type' => 'Permis de conduire',
                'status' => 'rejetée',
                'date' => '2024-01-10',
                'reference' => 'PER-2024-000890'
            ]
        ];

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

        return redirect()->route('citizen.dashboard')->with('success', 'Votre demande a été soumise avec succès');
    }

    public function documents()
    {
        $documents = [
            [
                'id' => 1,
                'type' => 'Carte Nationale d\'Identité',
                'reference' => 'CNI-2024-001234',
                'issue_date' => '2024-01-20',
                'expiry_date' => '2034-01-20',
                'status' => 'valide'
            ]
        ];

        return view('citizen.documents', compact('documents'));
    }

    public function downloadDocument($id)
    {
        return response()->download(storage_path('app/documents/document_' . $id . '.pdf'));
    }
}
