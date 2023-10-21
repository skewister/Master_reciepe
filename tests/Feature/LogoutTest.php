<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout()
    {
        // Créer un utilisateur
        $user = User::factory()->create();

        // Se connecter en tant qu'utilisateur
        $this->actingAs($user, 'api');

        // Déconnecter l'utilisateur
        $response = $this->postJson('/api/logout');

        // Vérifier que la réponse est correcte
        $response->assertStatus(200);

        // Vérifier que l'utilisateur ne peut pas accéder à une route protégée
        $response = $this->getJson('/api/user');
        $response->assertStatus(401); // ou 403 selon votre configuration
    }
}
