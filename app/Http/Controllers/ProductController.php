<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    //creation des methodes pour la gestion des produits
    public function index()
    {
        //
        //je recupere les produits avec leurs fournisseurs
        $products = \App\Models\Product::with('supplier')
        ->orderBy('name', 'asc')
        ->paginate(10);

        //retour de la vue avec les produits
        return view('products.index', compact('products'));
    }
}
