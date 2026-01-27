<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement; // INDISPENSABLE pour la traçabilité

class ProductController extends Controller
{
    // --- GESTION DES PRODUITS ET FILTRES 
    public function index(Request $request)
    {
        // 1. On prépare la requête (Query Builder)
        $query = Product::with('supplier');

        // 2. Filtre : Recherche texte (Nom ou Code Barre)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
        }

        // 3. Filtre : Type de produit (Vin, Bière...)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 4. Filtre : Fournisseur
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // 5. Exécution de la requête avec pagination
        $products = $query->orderBy('name', 'asc')->paginate(10);

        // 6. Récupération des données pour les menus déroulants (Select)
        $suppliers = Supplier::orderBy('name')->get();
        // On récupère la liste des types existants sans doublons
        $types = Product::select('type')->distinct()->whereNotNull('type')->pluck('type');

        // Retour de la vue avec toutes les données
        return view('products.index', compact('products', 'suppliers', 'types'));
    }


    // --- GESTION DES STOCKS AVEC TRAÇABILITÉ ---
    public function updateStock(Request $request, $id)
    {
        $vretour = [];
        $product = Product::findOrFail($id);
        $action = $request->input('action');
        
        $quantityChange = 0; // Variable pour noter de combien ça a bougé (+1 ou -1)

        if($action == '+') {
            $product->stock = $product->stock + 1;
            $quantityChange = 1; // On a ajouté 1
            $vretour['message'] = "stock ajouté";
        }
        else {
            if($action == '-') {
                if($product->stock > 0) {
                    $product->stock = $product->stock - 1;
                    $quantityChange = -1; // On a retiré 1
                    $vretour['message'] = "stock retiré";
                } else {
                    $vretour['message'] = "Plus de stocks";
                    $quantityChange = 0; // Pas de mouvement si stock à 0
                }
            }
        }

        $product->save();

        // --- ENREGISTREMENT DANS L'HISTORIQUE (TRAÇABILITÉ) ---
        // On n'enregistre que si le stock a réellement bougé
        if ($quantityChange !== 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'quantity'   => $quantityChange, // +1 ou -1
                'type'       => 'ajustement_rapide' // On précise que c'est fait via les boutons +/-
            ]);
        }
        // ------------------------------------------------------

        $vretour['success'] = true;
        $vretour['newStock'] = $product->stock;
        $vretour['product_id'] = $product->id;

        return response()->json($vretour);
    }


    // --- GENERATION DU PDF (Optimisé) ---
    public function exportPdf()
    {
        // Augmentation de la mémoire et du temps d'execution
        ini_set('memory_limit', '-1');
        set_time_limit(300); // 5 minutes

        // 1. Récupérer les données (Limité à 300 pour performance)
        $products = Product::with('supplier')
            ->orderBy('name', 'asc')
            ->limit(300) 
            ->get();

        // 2. Charger la vue PDF
        $pdf = Pdf::loadView('products.pdf', compact('products'));

        // 3. Télécharger
        return $pdf->download('inventaire_cavevin.pdf');
    }

    // --- AFFICHAGE DU CALENDRIER ---
    public function calendar()
    {
        // 1. On récupère tous les mouvements avec le nom du produit
        $movements = StockMovement::with('product')->get();

        // 2. On transforme les données pour FullCalendar (Format JSON)
        $events = [];

        foreach ($movements as $m) {
            // Si c'est un ajout (+), couleur verte. Sinon rouge.
            $color = $m->quantity > 0 ? '#198754' : '#dc3545';
            
            // On construit le titre : "Verre (+5)"
            $signe = $m->quantity > 0 ? '+' : '';
            $titre = $m->product->name . " (" . $signe . $m->quantity . ")";

            $events[] = [
                'title' => $titre,
                'start' => $m->created_at->toIso8601String(), // Format de date ISO
                'color' => $color,
                // 'allDay' => false // Pour voir l'heure exacte
            ];
        }

        // 3. On envoie les données à la vue
        return view('products.calendar', compact('events'));
    }
}