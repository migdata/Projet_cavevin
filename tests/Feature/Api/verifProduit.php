<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Supplier;


class verifProduit extends TestCase
{

use RefreshDatabase;// pour remettre a zero la bdd a chaque test

    protected $unProduit, $unFournisseur,$produitRecherche;


    public function setUp(): void
    {   
        parent::setUp();    
        // je cree un fournisseur en bdd pour mes tests
        $this->unFournisseur = new Supplier();
        $this->unFournisseur->id = 1;                       
        $this->unFournisseur->name = "Fournisseur Test";
        $this->unFournisseur->address = "123 rue de Test";
        $this->unFournisseur->email = "contact@fournisseur-test.com";       
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


        // test de produit de recherche 
        $this->produitRecherche = new Product();
        $this->produitRecherche->name = "rouge Test";
        $this->produitRecherche->description = "description du rouge test";
        $this->produitRecherche->price = 15.75;
        $this->produitRecherche->stock = 50;
        $this->produitRecherche->type = "rouge";
        $this->produitRecherche->barcode = "9876543210987";
        $this->produitRecherche->supplier_id = $this->unFournisseur->id; // id du fournisseur (doit exister en bdd)
        $this->produitRecherche->save();
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    public function test_liste_produit_api()
    {
        //le robot testeur envoie une requete get a l'api pour recuperer la liste des produits
        $response = $this->getJson('/api/products');

        //je m'attends a recevoir un code 200 (ok)
        $response->assertStatus(200);

        //je m'attends a recevoir une reponse en json avec success = true et au moins 1 produit
        $response->assertJson([
            'success' => true,
            'message' => "Liste des produits",  
        ]);

        $this->assertCount(2, $response->json('data')); // je m'attends a avoir au moins 1 produit dans data
    }

    public function  test_recherche_produit_api()
    {
        //le robot testeur envoie une requete get a l'api pour rechercher un produit
        $response = $this->getJson('/api/products?search=vin');

        //je m'attends a recevoir un code 200 (ok)
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 1 
        ]);
        $response->assertJsonFragment([
            'name' => 'vin Test'
        ]);
        $response->assertJsonMissing([
            'name' => 'rouge Test']); // je ne dois pas trouver le produit de recerche rouge
    }

    public function test_produit_unique_api()
    {
        //le robot testeur envoie une requete get a l'api pour recuperer un produit par son id
        $response = $this->getJson('/api/products/'. $this->unProduit->id);

        //je m'attends a recevoir un code 200 (ok)
        $response->assertStatus(200);

        //je m'attends a recevoir une reponse en json avec success = true et le produit demandé
        $response->assertJson([
            'success' => true,
            'message' => "Produit trouvé avec succès",  
            'data' => [
                'id' => $this->unProduit->id,
                'name' => $this->unProduit->name,
                'description' => $this->unProduit->description,
                'price' => $this->unProduit->price,
                'stock' => $this->unProduit->stock,
                'type' => $this->unProduit->type,
                'barcode' => $this->unProduit->barcode,
                'supplier_id' => $this->unProduit->supplier_id,
            ]
        ]);
    }

    public function test_404_produit_non_trouve_api()
    {
       
        //erreur 404 - produit non trouvé
    //le robot testeur envoie une requete get a l'api pour recuperer un produit inexistant
        $response = $this->getJson('/api/products/9999'); // id qui n'existe pas

        //je m'attends a recevoir un code 404 (not found)
        $response->assertStatus(404);

        //je m'attends a recevoir une reponse en json avec success = false
        $response->assertJson([
            'success' => false,
            'message' => "Produit non trouvé",  
            'data' => null
        ]);
    }
}

