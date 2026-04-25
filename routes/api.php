<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
        Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        
        // Document requests
        Route::prefix('document-requests')->group(function () {
            Route::get('/', [DocumentController::class, 'index']);
            Route::post('/', [DocumentController::class, 'store']);
            Route::get('/{id}', [DocumentController::class, 'show']);
            Route::put('/{id}', [DocumentController::class, 'update']);
            Route::delete('/{id}', [DocumentController::class, 'destroy']);
        });

        // Documents
        Route::prefix('documents')->group(function () {
            Route::get('/', [DocumentController::class, 'documents']);
            Route::get('/{id}', [DocumentController::class, 'showDocument']);
            Route::get('/{id}/download', [DocumentController::class, 'download']);
        });

        // User profile
        Route::prefix('profile')->group(function () {
            Route::get('/', [AuthController::class, 'profile']);
            Route::put('/', [AuthController::class, 'updateProfile']);
            Route::put('/password', [AuthController::class, 'updatePassword']);
        });
    });

    // Public verification route
    Route::prefix('verification')->group(function () {
        Route::post('/verify', [VerificationController::class, 'verify']);
        Route::get('/verify/{code}', [VerificationController::class, 'verifyByCode']);
    });

    // Admin routes (protected by admin middleware)
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::get('/stats', [DocumentController::class, 'stats']);
        Route::get('/document-requests/pending', [DocumentController::class, 'pending']);
        Route::post('/document-requests/{id}/validate', [DocumentController::class, 'validateDocument']);
        Route::post('/document-requests/{id}/reject', [DocumentController::class, 'reject']);
    });
});

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'service' => 'IdentiGuinée API',
    ]);
});

// API documentation
Route::get('/', function () {
    return response()->json([
        'name' => 'IdentiGuinée API',
        'version' => '1.0.0',
        'description' => 'API pour la plateforme nationale d\'identité numérique de la Guinée',
        'endpoints' => [
            'auth' => '/api/v1/auth',
            'document-requests' => '/api/v1/document-requests',
            'documents' => '/api/v1/documents',
            'verification' => '/api/v1/verification',
            'admin' => '/api/v1/admin',
        ],
        'documentation' => url('/api/docs'),
        'health_check' => url('/api/health'),
    ]);
});
