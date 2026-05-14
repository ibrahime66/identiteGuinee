<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VerifierController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/debug-auth', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'host' => $request->getHost(),
        'full_url' => $request->fullUrl(),
        'session_id' => $request->session()->getId(),
        'auth_check' => \Illuminate\Support\Facades\Auth::check(),
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'role' => \Illuminate\Support\Facades\Auth::user()?->role,
        'email' => \Illuminate\Support\Facades\Auth::user()?->email,
    ]);
});

// Connexion unique pour tous les rôles
Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
Route::post('/connexion', [AuthController::class, 'login']);

Route::prefix('citoyen')->name('citizen.')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showCitizenRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'citizenRegister']);
    Route::get('/dashboard', [CitizenController::class, 'dashboard'])->name('dashboard')->middleware('citizen.auth');
    Route::get('/demande', [CitizenController::class, 'createRequest'])->name('request.create')->middleware('citizen.auth');
    Route::post('/demande', [CitizenController::class, 'storeRequest'])->name('request.store')->middleware('citizen.auth');
    Route::get('/documents', [CitizenController::class, 'documents'])->name('documents')->middleware('citizen.auth');
    Route::get('/profil', [CitizenController::class, 'profile'])->name('profile')->middleware('citizen.auth');
    Route::get('/profil/modifier', [CitizenController::class, 'editProfile'])->name('profile.edit')->middleware('citizen.auth');
    Route::post('/profil/modifier', [CitizenController::class, 'updateProfile'])->name('profile.update')->middleware('citizen.auth');
    Route::get('/mot-de-passe/changer', [CitizenController::class, 'showChangePassword'])->name('password.change')->middleware('citizen.auth');
    Route::post('/mot-de-passe/changer', [CitizenController::class, 'changePassword'])->name('password.update')->middleware('citizen.auth');
    Route::get('/telecharger/{reference}', [CitizenController::class, 'downloadDocument'])->name('download')->middleware('citizen.auth');
    Route::delete('/document/{reference}/supprimer', [CitizenController::class, 'deleteDocument'])->name('document.delete')->middleware('citizen.auth');
    Route::post('/deconnexion', [AuthController::class, 'citizenLogout'])->name('logout');
});

Route::prefix('administration')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard')->middleware('admin.auth');
    Route::get('/demandes', [AdminController::class, 'requests'])->name('requests')->middleware('admin.auth');
    Route::get('/demande/{id}', [AdminController::class, 'showRequest'])->name('request.show')->middleware('admin.auth');
    Route::post('/demande/{id}/valider', [AdminController::class, 'validateRequest'])->name('request.validate')->middleware('admin.auth');
    Route::post('/demande/{id}/rejeter', [AdminController::class, 'rejectRequest'])->name('request.reject')->middleware('admin.auth');
    Route::get('/rapports', [AdminController::class, 'reports'])->name('reports')->middleware('admin.auth');
    Route::get('/parametres', [AdminController::class, 'settings'])->name('settings')->middleware('admin.auth');
    Route::post('/parametres/vider-cache', [AdminController::class, 'clearCache'])->name('settings.clear-cache')->middleware('admin.auth');
    Route::get('/parametres/exporter', [AdminController::class, 'exportData'])->name('settings.export')->middleware('admin.auth');
    Route::post('/parametres/sauvegarder', [AdminController::class, 'backupDatabase'])->name('settings.backup')->middleware('admin.auth');
    Route::post('/deconnexion', [AuthController::class, 'adminLogout'])->name('logout');
});

Route::prefix('verification')->name('verifier.')->group(function () {
    Route::get('/', [VerifierController::class, 'index'])->name('index');
    Route::post('/verifier', [VerifierController::class, 'verify'])->name('verify');
});
