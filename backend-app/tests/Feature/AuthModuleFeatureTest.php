<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthModuleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_emite_token_y_persiste_usuario()
    {
        $payload = [
            'name' => 'Usuario Nuevo',
            'email' => 'nuevo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'nuevo@test.com']);
    }

    public function test_login_entrega_token_con_credenciales_validas()
    {
        $user = User::factory()->create([
            'email' => 'login@test.com',
            'password' => Hash::make('segura123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@test.com',
            'password' => 'segura123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
            ]);
    }

    public function test_login_rechaza_credenciales_invalidas()
    {
        User::factory()->create([
            'email' => 'fallo@test.com',
            'password' => Hash::make('correcta'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'fallo@test.com',
            'password' => 'incorrecta',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized']);
    }
}
