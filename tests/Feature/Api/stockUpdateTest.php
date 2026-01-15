<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;


class stockUpdateTest extends TestCase
{
    use RefreshDatabase;// pour remettre a zero la bdd a chaque test

    protected $unProduit, $unFournisseur;

    public function setUp(): void
    {


        parent::setUp();

    // je cree un fournisseur en bdd pour mes tests
        $this->unFournisseur = new Supplier();
        $this->unFournisseur->id = 10;
        $this->unFournisseur->name = "Fournisseur Test";
        $this->unFournisseur->address = "123 rue de Test";
        $this->unFournisseur->email = "contact@test.com";
        $this->unFournisseur->phone = "0123456789";
        $this->unFournisseur->contact_com = "Aucun com";  
        $this->unFournisseur->save();


        // je cree un produit en bdd pour mes tests
        $this->unProduit = new Product();
        $this->unProduit->name = "vin Test";  
        $this->unProduit->description = "description du vin test";
        $this->unProduit->price = 10.50;
        $this->unProduit->stock = 100;
        $this->unProduit->type = "vin";
        $this->unProduit->barcode = "1234567890123";
        $this->unProduit->supplier_id = $this->unFournisseur->id; // id du fournisseur (doit exister en bdd)
        $this->unProduit->save();

    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_stock_update_api_success()
    {
        //le robot
        // envoie une requete pour augmenter le stock
        $response = $this->putJson('/api/products/'. $this->unProduit->id, [
            'stock' => 50
        ]);

        
        $response->assertStatus(200); // est ce que le serveur a bien répondu

        //vérification du contenu du retour
        $response->assertJson([
            'success' => true,
            'message' => 'Stock mis à jour avec succès',
            'data' => [
                'id' => $this->unProduit->id,
                'stock' => 50
            ]
        ]); 

        // vérification en base de données
        $this->assertDatabaseHas('products', [
            'id' => $this->unProduit->id,
            'stock' => 50
        ]); 
    }

    public function test_stock_update_api_failure()
    {       


        //le robot
        // envoie une requete pour augmenter le stock avec une valeur invalide
        $response = $this->putJson('/api/products/'. $this->unProduit->id, [
            'stock' => -10 // je ne peux pas avoir un stock négatif
        ]);

        
        $response->assertStatus(422); //valeur non acceptable

        //vérification du contenu du retour
        $response->assertJson([
            'success' => false,
            'message' => 'La valeur du stock est invalide',
        ]); 

        // vérification en base de données que le stock n'a pas changé
        $this->assertDatabaseHas('products', [
            'id' => $this->unProduit->id,
            'stock' => $this->unProduit->stock // mon stock n'a pas changé
        ]);

    }
}
