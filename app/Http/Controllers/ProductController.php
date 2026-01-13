<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    //creation des methodes pour la gestion des produits
    public function index()
    {
        //code pour afficher la liste des produits
        //je recupere les produits avec leurs fournisseurs
        $products = \App\Models\Product::with('supplier')
        ->get()
        ->sortBy('name')
        ->paginate(10);

        //retour de la vue avec les produits
        return view('products.index', compact('products'));
    }
}
