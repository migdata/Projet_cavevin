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

    // gestion de stocks


   public function updateStock(Request $request, $id)
{
    $vretour = [];
    $product = \App\Models\Product::findOrFail($id);
    $action = $request->input('action');

   

    if($action == '+') {
        $product->stock = $product->stock + 1;
        $vretour['message'] = "stock ajouté";
    }
    else {
        if($action == '-') {
            if($product->stock > 0) {
                $product->stock = $product->stock - 1;
                $vretour['message'] = "stock retiré";
            } else {
                $vretour['message'] = "Plus de stocks";
            }
        }
    }

    $product->save();

    $vretour['success'] = true;
    $vretour['newStock'] = $product->stock;
    $vretour['product_id'] = $product->id;

    return response()->json($vretour);
}
}