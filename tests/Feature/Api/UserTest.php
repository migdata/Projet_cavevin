<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $admin, $user;

    public function setUp(): void
    {
        parent::setUp();

        // Créer un admin
        $this->admin = User::factory()->create([
            'name'  => 'Admin Test',
            'email' => 'admin@test.com',
            'role'  => 'admin',
        ]);

        // Créer un user simple
        $this->user = User::factory()->create([
            'name'  => 'User Test',
            'email' => 'user@test.com',
            'role'  => 'user',
        ]);
    }

    // --- LISTE ---
    public function test_liste_users_api()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertCount(2, $response->json('data'));
    }

    // --- CRÉER --- 
    public function test_admin_peut_creer_user()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/users', [
                'name'     => 'Nouveau User',
                'email'    => 'nouveau@test.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Utilisateur créé avec succès',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'nouveau@test.com',
        ]);
    }

    public function test_user_ne_peut_pas_creer_user()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/users', [
                'name'     => 'Nouveau User',
                'email'    => 'nouveau@test.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Accès refusé.',
        ]);
    }

    // --- MODIFIER ---
    public function test_admin_peut_modifier_user()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/users/' . $this->user->id, [
                'name' => 'User Modifié',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Utilisateur modifié avec succès',
        ]);

        $this->assertDatabaseHas('users', [
            'id'   => $this->user->id,
            'name' => 'User Modifié',
        ]);
    }

    public function test_user_ne_peut_pas_modifier_user()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/users/' . $this->admin->id, [
                'name' => 'Hacker',
            ]);

        $response->assertStatus(403);
    }

    // --- SUPPRIMER ---
    public function test_admin_peut_supprimer_user()
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson('/api/users/' . $this->user->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès',
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
        ]);
    }

    public function test_user_ne_peut_pas_supprimer_user()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/users/' . $this->admin->id);

        $response->assertStatus(403);
    }

    // --- 404 ---
    public function test_404_user_non_trouve()
{
    $response = $this->actingAs($this->admin, 'sanctum')
        ->deleteJson('/api/users/9999');

    $response->assertStatus(404);
    $response->assertJson([
        'success' => false,
        'message' => 'Utilisateur non trouvé',
    ]);
}
}