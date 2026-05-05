<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

class stockUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $unProduit, $unFournisseur, $admin;

    public function setUp(): void
    {
        parent::setUp();

        // Créer un admin pour les tests
       $this->admin = User::factory()->create([
         'name'     => 'Admin Test',
         'email'    => 'admin@test.com',
         'password' => bcrypt('password'),
         'role'     => 'admin',
]);

        // Créer un fournisseur
        $this->unFournisseur = new Supplier();
        $this->unFournisseur->id = 10;
        $this->unFournisseur->name = "Fournisseur Test";
        $this->unFournisseur->address = "123 rue de Test";
        $this->unFournisseur->email = "contact@test.com";
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
    }

    public function test_example(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_stock_update_api_success()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/products/' . $this->unProduit->id, [
                'stock' => 50
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Stock mis à jour avec succès',
            'data'    => [
                'id'    => $this->unProduit->id,
                'stock' => 50
            ]
        ]);

        $this->assertDatabaseHas('products', [
            'id'    => $this->unProduit->id,
            'stock' => 50
        ]);
    }

    public function test_stock_update_api_failure()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/products/' . $this->unProduit->id, [
                'stock' => -10
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'La valeur du stock est invalide',
        ]);

        $this->assertDatabaseHas('products', [
            'id'    => $this->unProduit->id,
            'stock' => $this->unProduit->stock
        ]);
    }
}