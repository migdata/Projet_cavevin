<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

// Route publique avec rate limiting
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('logAction:login,User');
});

// Routes protégées
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {


    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('logAction:logout,User');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Produits — lecture pour tout le monde
    Route::get('/products', [ProductController::class, 'ListeProduits']);
    Route::get('/products/{id}', [ProductController::class, 'unProduit']);

    // Produits — admin seulement
    Route::middleware('isAdmin')->group(function () {
        Route::post('/products', [ProductController::class, 'store'])
            ->middleware('logAction:create_product,Product');
        Route::put('/products/{id}', [ProductController::class, 'updateStock'])
            ->middleware('logAction:update_stock,Product');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])
            ->middleware('logAction:delete_product,Product');
    });

    // Utilisateurs — lecture pour tout le monde
    Route::get('/users', [UserController::class, 'index']);

    // Utilisateurs — admin seulement
    Route::middleware('isAdmin')->group(function () {
        Route::post('/users', [UserController::class, 'store'])
            ->middleware('logAction:create_user,User');
        Route::put('/users/{id}', [UserController::class, 'update'])
            ->middleware('logAction:update_user,User');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])
            ->middleware('logAction:delete_user,User');
    });

    // Logs — admin seulement
Route::middleware('isAdmin')->get('/logs', function () {
    $logs = \App\Models\ActionLog::with('user')
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();

    return response()->json([
        'success' => true,
        'data'    => $logs
    ]);
});
});