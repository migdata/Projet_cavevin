<?php

// verif des colonnes de la table products

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;

class ProductTest extends TestCase
{
    /**
     * verifie que la table products possede bien les colonnes attendues 
     *  pour l'importation des produits
     */
    public function test_products_approved(): void
    {

        //
        $product = new Product();

        $colonnes_attendues = [
                         'name', 
                         'description',
                          'price', 
                          'stock', 
                           'barcode',
                            'supplier_id', 
                        ];



                        // on recupere le fillable du model Product
                        $fillable = $product->getFillable();
            // je verifie que chaque colonne attendue est bien presente dans la table products
            $this->assertEquals($colonnes_attendues,  $fillable);
       
    }
}