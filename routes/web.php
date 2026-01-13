<?php

use Illuminate\Support\Facades\Route;
// ajout de la route du controller des produits
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stocks', [ProductController::class, 'index'])->name('products.index');
