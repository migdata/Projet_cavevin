<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Imports des Contrôleurs
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LoginController; // <--- On ajoute le contrôleur de connexion

/*
|--------------------------------------------------------------------------
| 1. ROUTES PUBLIQUES (Accessibles sans connexion)
|--------------------------------------------------------------------------
*/

// Redirection de la page d'accueil vers le login
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes de Connexion (Login)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');


/*
|--------------------------------------------------------------------------
| 2. ROUTES PROTÉGÉES (Nécessite d'être connecté)
|--------------------------------------------------------------------------
| Le middleware 'auth' vérifie l'identité.
*/
Route::middleware('auth')->group(function () {

    // Déconnexion
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- GESTION DES PRODUITS ---
    
    // Liste des produits (Page principale)
    Route::get('/admin/products', [ProductController::class, 'index'])->name('products.index');

    // Mise à jour du stock (+/-) via AJAX
    Route::post('/products/{id}/update-stock', [ProductController::class, 'updateStock'])->name('products.updateStock');

    // --- IMPORT / EXPORT ---

    // Traitement du fichier CSV (Import DGSYS)
    Route::post('/admin/import', [ImportController::class, 'processImport'])->name('import.process');

    // Téléchargement PDF
    Route::get('/admin/export-pdf', [ProductController::class, 'exportPdf'])->name('exportPdf');

    // --- TRAÇABILITÉ ---

    // Calendrier des mouvements
    Route::get('/admin/calendar', [ProductController::class, 'calendar'])->name('products.calendar');

});