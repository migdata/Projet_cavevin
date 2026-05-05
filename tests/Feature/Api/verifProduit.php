<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;

class verifProduit extends TestCase
{
    use RefreshDatabase;

    protected $unProduit, $unFournisseur, $produitRecherche, $user;

    public function setUp(): void
    {
        parent::setUp();

        // Créer un user simple pour les tests
        $this->user = User::factory()->create([
            'name'  => 'User Test',
            'email' => 'user@test.com',
            'role'  => 'user',
        ]);

        // Créer un fournisseur
        $this->unFournisseur = new Supplier();
        $this->unFournisseur->id = 1;
        $this->unFournisseur->name = "Fournisseur Test";
        $this->unFournisseur->address = "123 rue de Test";
        $this->unFournisseur->email = "contact@fournisseur-test.com";
        $this->unFournisseur->phone = "0123456789";
        $this->unFournisseur->contact_com = "Aucun com";
        $this->unFournisseur->save();

        // Créer un produit
        $this->unProduit = new Product();
        $this->unProduit->name = "vin Test";
        $this->unProduit->description = "description du vin test";
        $this->unProduit->price = 10.50;
        $this->unProduit->stock = 100;
        $this->unProduit->type = "vin";
        $this->unProduit->barcode = "1234567890123";
        $this->unProduit->supplier_id = $this->unFournisseur->id;
        $this->unProduit->save();

        // Créer un produit de recherche
        $this->produitRecherche = new Product();
        $this->produitRecherche->name = "rouge Test";
        $this->produitRecherche->description = "description du rouge test";
        $this->produitRecherche->price = 15.75;
        $this->produitRecherche->stock = 50;
        $this->produitRecherche->type = "rouge";
        $this->produitRecherche->barcode = "9876543210987";
        $this->produitRecherche->supplier_id = $this->unFournisseur->id;
        $this->produitRecherche->save();
    }

    public function test_example(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_liste_produit_api()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => "Liste des produits",
        ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_recherche_produit_api()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products?search=vin');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 1
        ]);
        $response->assertJsonFragment([
            'name' => 'vin Test'
        ]);
        $response->assertJsonMissing([
            'name' => 'rouge Test'
        ]);
    }

    public function test_produit_unique_api()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products/' . $this->unProduit->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => "Produit trouvé avec succès",
            'data'    => [
                'id'          => $this->unProduit->id,
                'name'        => $this->unProduit->name,
                'description' => $this->unProduit->description,
                'price'       => $this->unProduit->price,
                'stock'       => $this->unProduit->stock,
                'type'        => $this->unProduit->type,
                'barcode'     => $this->unProduit->barcode,
                'supplier_id' => $this->unProduit->supplier_id,
            ]
        ]);
    }

    public function test_404_produit_non_trouve_api()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products/9999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => "Produit non trouvé",
            'data'    => null
        ]);
    }
}