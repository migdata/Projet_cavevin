<?php

use Illuminate\Support\Facades\Route;
// ajout de la route du controller des produits
use App\Http\Controllers\ProductController;

// ajout de la route du controller d'import de produits de l'applcation DGSYS
use App\Http\Controllers\ImportController;

use Illuminate\Support\Facades\App;

Route::get('/', function () {
    return view('welcome');
});

// routes pour la gestion des produits et des stocks
Route::get('/stocks', [ProductController::class, 'index'])
->name('products.index');

// mise a jour du stock via api ajax
Route::post('/products/{id}/update-stock', [ProductController::class, 'updateStock'])
->name('products.updateStock');

// route pour traiter les fichiers csv depuis le formulaire 

Route::post('/admin/import', [ImportController::class, 'processImport'])->name('import.process');