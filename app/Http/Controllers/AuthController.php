<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role === 'citizen') {
                Session::put('citizen_authenticated', true);
                Session::put('citizen_name', $user->name);
                Session::put('citizen_email', $user->email);
                Session::put('citizen_id', $user->id);
                
                return redirect()->route('citizen.dashboard')->with('success', 'Connexion réussie');
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'Accès non autorisé pour ce compte'])->withInput();
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|string|max:20',
            'cni_number' => 'required|string|max:20'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'cni_number' => $request->cni_number,
            'role' => 'citizen'
        ]);

        Auth::login($user);
        
        Session::put('citizen_authenticated', true);
        Session::put('citizen_name', $user->name);
        Session::put('citizen_email', $user->email);
        Session::put('citizen_id', $user->id);
        
        return redirect()->route('citizen.dashboard')->with('success', 'Inscription réussie');
    }

    public function citizenLogout()
    {
        Auth::logout();
        Session::forget(['citizen_authenticated', 'citizen_name', 'citizen_email', 'citizen_id']);
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

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                Session::put('admin_authenticated', true);
                Session::put('admin_name', $user->name);
                Session::put('admin_id', $user->id);
                
                return redirect()->route('admin.dashboard')->with('success', 'Connexion administrateur réussie');
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'Accès non autorisé pour ce compte'])->withInput();
        }

        return back()->withErrors(['email' => 'Identifiants administrateur incorrects'])->withInput();
    }

    public function adminLogout()
    {
        Auth::logout();
        Session::forget(['admin_authenticated', 'admin_name', 'admin_id']);
        return redirect()->route('admin.login')->with('success', 'Déconnexion réussie');
    }
}
