<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //creation des methodes pour la gestion des produits
    public function ListeProduits( Request $request)
    {
        //
        $vretour = [];

        $maRecherche = $request->input('search'); // recuperation du terme de recherche (/api/products?search=rouge)

        if($maRecherche)
        {
            // si j'ai un terme de recherche, je filtre les produits
            $products = Product::where('name', 'LIKE', "%$maRecherche%")
                                ->orWhere('type', 'LIKE', "%$maRecherche%")
                                ->get();
            $message  = "Resultats de la recherche pour '$maRecherche'";
        }
        else
             {
                $products = Product::all(); // renvoie moi tous les produits si tu n'as pas de terme de recherche
                $message  = "Liste des produits";
            
             }
             
        if($products->count() > 0)
            {
                $vretour['success'] = true;
                $vretour['message'] = $message;
                $vretour['total'] = $products->count();
                $vretour['data'] = $products; // les données des produits recupérées depuis la bdd 
             
            }
            else {
                $vretour['success'] = false;
                $vretour['message'] = "Aucun produit trouvé";
                $vretour['total'] = 0;
                $vretour['data'] = [];
            }

            return response()->json($vretour, 200); // cher ami rettourne moi le tableau que 
                                            // que j'ai construit en langage json (serveur)
        }


        public function unProduit($id)
        {
            //

            $vretour = [];

            $unProduit = Product::find($id); // je recupere son id

            if($unProduit)
                {
                    $vretour['success'] = true;
                    $vretour['message'] = "Produit trouvé avec succès";
                    $vretour['data'] = $unProduit;    
                    
                 
                }
                else {
                    $vretour['success'] = false;
                    $vretour['message'] = "Produit non trouvé";
                    $vretour['data'] = null;
                }

                return response()->json($vretour, $vretour['success'] ? 200 : 404); 
        }

        public function updateStock(Request $request, $id)
        {
            //
            $vretour = [];

            $leProduct = Product::find($id); // je recupere son id

            if(!$leProduct)
            {
                $vretour['success'] = false;
                $vretour['message'] = "Produit non trouvé";
                return response()->json($vretour, 404);
            }
            
            //empecher les stocks negatif
            $valeurNegativeStock = Validator::make($request->all(), [
                'stock' => 'required|integer|min:0'
            ]);

            if($valeurNegativeStock->fails())
            {
                $vretour['success'] = false;
                $vretour['message'] = "La valeur du stock est invalide";
                return response()->json($vretour, 422);
            }

             // mise a jour du stock
            $leProduct->stock = $request->input('stock');       
            $leProduct->save();
            
            // retour reussi

            $vretour['success'] = true;
            $vretour['message'] = "Stock mis à jour avec succès";
            $vretour['data'] = [
                'id' => $leProduct->id,
                'stock' => $leProduct->stock
            ];

            return response()->json($vretour, 200);
        }
}

