<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showCitizenLogin()
    {
        return view('auth.citizen-login');
    }

    public function citizenLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($request->email === 'citoyen@identiguinee.gn' && $request->password === 'password') {
            Session::put('citizen_authenticated', true);
            Session::put('citizen_name', 'Mamadou Diallo');
            Session::put('citizen_email', $request->email);
            
            return redirect()->route('citizen.dashboard')->with('success', 'Connexion réussie');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects'])->withInput();
    }

    public function showCitizenRegister()
    {
        return view('auth.citizen-register');
    }

    public function citizenRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:citizens,email',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|string|max:20',
            'cni' => 'required|string|max:20'
        ]);

        Session::put('citizen_authenticated', true);
        Session::put('citizen_name', $request->name);
        Session::put('citizen_email', $request->email);
        
        return redirect()->route('citizen.dashboard')->with('success', 'Inscription réussie');
    }

    public function citizenLogout()
    {
        Session::forget(['citizen_authenticated', 'citizen_name', 'citizen_email']);
        return redirect()->route('citizen.login')->with('success', 'Déconnexion réussie');
    }

    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($request->email === 'admin@identiguinee.gn' && $request->password === 'admin123') {
            Session::put('admin_authenticated', true);
            Session::put('admin_name', 'Administrateur Principal');
            
            return redirect()->route('admin.dashboard')->with('success', 'Connexion administrateur réussie');
        }

        return back()->withErrors(['email' => 'Identifiants administrateur incorrects'])->withInput();
    }

    public function adminLogout()
    {
        Session::forget(['admin_authenticated', 'admin_name']);
        return redirect()->route('admin.login')->with('success', 'Déconnexion réussie');
    }
}
