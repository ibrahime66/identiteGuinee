<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VerifierController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('citoyen')->name('citizen.')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showCitizenLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'citizenLogin']);
    Route::get('/inscription', [AuthController::class, 'showCitizenRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'citizenRegister']);
    Route::get('/dashboard', [CitizenController::class, 'dashboard'])->name('dashboard')->middleware('citizen.auth');
    Route::get('/demande', [CitizenController::class, 'createRequest'])->name('request.create')->middleware('citizen.auth');
    Route::post('/demande', [CitizenController::class, 'storeRequest'])->name('request.store')->middleware('citizen.auth');
    Route::get('/documents', [CitizenController::class, 'documents'])->name('documents')->middleware('citizen.auth');
    Route::get('/telecharger/{id}', [CitizenController::class, 'downloadDocument'])->name('download')->middleware('citizen.auth');
    Route::post('/deconnexion', [AuthController::class, 'citizenLogout'])->name('logout');
});

Route::prefix('administration')->name('admin.')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showAdminLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'adminLogin']);
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard')->middleware('admin.auth');
    Route::get('/demandes', [AdminController::class, 'requests'])->name('requests')->middleware('admin.auth');
    Route::get('/demande/{id}', [AdminController::class, 'showRequest'])->name('request.show')->middleware('admin.auth');
    Route::post('/demande/{id}/valider', [AdminController::class, 'validateRequest'])->name('request.validate')->middleware('admin.auth');
    Route::post('/demande/{id}/rejeter', [AdminController::class, 'rejectRequest'])->name('request.reject')->middleware('admin.auth');
    Route::post('/deconnexion', [AuthController::class, 'adminLogout'])->name('logout');
});

Route::prefix('verification')->name('verifier.')->group(function () {
    Route::get('/', [VerifierController::class, 'index'])->name('index');
    Route::post('/verifier', [VerifierController::class, 'verify'])->name('verify');
});
