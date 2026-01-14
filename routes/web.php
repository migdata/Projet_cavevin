<?php

use Illuminate\Support\Facades\Route;
// ajout de la route du controller des produits
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\App;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stocks', [ProductController::class, 'index'])
->name('products.index');

Route::post('/products/{id}/update-stock', [ProductController::class, 'updateStock'])
->name('products.updateStock');
