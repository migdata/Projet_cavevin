<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// route pour lister les produits via mon API
Route::get('/products', [ProductController::class, 'ListeProduits']);

// route pour afficher un produit via mon API
Route::get(('/products/{id}'), [ProductController::class, 'unProduit']);

// route pour mettre a jour le stock d'un produit via mon API
Route::put('/products/{id}', [ProductController::class, 'updateStock']);

// Tout le monde peut essayer de se connecter 
 

Route::post('/login', [AuthController::class, 'login']);

// acces protegés
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});