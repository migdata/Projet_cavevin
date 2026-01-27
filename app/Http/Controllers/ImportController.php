<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // On charge ton modèle Product

class ImportController extends Controller
{
    /**
     * Traite le fichier CSV envoyé par le formulaire
     */
    public function processImport(Request $request)
    {
        // 1. Validation : On vérifie qu'on a bien reçu un fichier
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx',
        ]);

        // 2. Ouverture du fichier
        $file = $request->file('csv_file');
        
        // On ouvre le fichier en mode lecture ('r')
        if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
            
            // On saute la première ligne (les titres : "Etat stock, Type...")
            fgetcsv($handle, 1000, ','); 

            $count = 0; // Compteur pour savoir combien de produits on a traité

            // 3. Lecture ligne par ligne
            // On boucle tant qu'il y a des lignes dans le fichier
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                
                // --- MAPPING (La carte au trésor) ---
                // On relie les colonnes de ton fichier DGSYS à tes variables
                
                // Colonne 8 : Le Code Barre (C'est notre clé unique)
                $barcode = $data[8] ?? null; 
                
                // Colonne 4 : Nom de l'article
                $nom = $data[4] ?? 'Produit Inconnu';
                
                // Colonne 7 : Prix de base (Ex: 9.8)
                $prix = $data[7] ?? 0;
                
                // Colonne 0 : Etat stock (La quantité)
                $stock = $data[0] ?? 0;
                
                // Colonne 3 : Sous famille (Ex: COMPTOIRE DES SOMMELIERS)
                $description = $data[3] ?? '';

                // colonne 2 : Type (Ex: Vin Rouge, Vin Blanc, Champagne, Spiritueux, etc.)
                $type = $data[2] ?? 'Inconnu';

                // Sécurité : Si pas de code barre, on ignore la ligne
                if (empty($barcode)) {
                    continue; 
                }

                // Nettoyage du prix : DGSYS peut mettre des virgules, PHP veut des points
                $prix = str_replace(',', '.', $prix);

                // 4. Update or Create (La commande magique)
                // Laravel cherche le produit avec ce code barre.
                // S'il existe -> Il met à jour le stock et le prix.
                // S'il n'existe pas -> Il le crée.
                Product::updateOrCreate(
                    ['barcode' => $barcode], // Critère de recherche
                    [
                        'name'           => $nom,
                        'price'          => (float) $prix,
                        'stock'          => (int) $stock, // Ton modèle utilise bien 'stock'
                        'description'    => $description,
                        'type'           => $type,

                        // 'supplier_id' => 1 // On pourrait mettre un fournisseur par défaut
                        'supplier_id'   => $defaultSupplierId = 1, // ID du fournisseur par défaut 
                        
                    ]
                );

                $count++;
            }
            
            // On ferme le fichier une fois fini
            fclose($handle);
        }

        // 5. Succès ! On retourne à la liste avec un message
        return redirect()->route('products.index')
                         ->with('success', "Succès ! $count produits importés depuis DGSYS.");
    }
}