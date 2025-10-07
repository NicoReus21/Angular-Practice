<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

     protected User $user;
    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario de prueba
        $this->user = User::factory()->create([
            'email' => 'usuario@test.com',
            'password' => bcrypt('password123'),
        ]);
    }
    public function test_login_con_credenciales_correctas_devuelve_token()
    {


        // Llamada POST al endpoint de login
        $response = $this->postJson('/api/login', [
            'email' => 'usuario@test.com',
            'password' => 'password123',
        ]);
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'user' => [
                     'id',
                     'name',
                     'email',
                 ],
                 'token'
             ]);
    }
    public function test_login_con_credenciales_incorrectas_devuelve_error()
{
    $response = $this->postJson('/api/login', [
        'email' => 'usuario@test.com',
        'password' => 'password_incorrecta',
    ]);

    $response->assertStatus(401)
             ->assertJson([
                 'message' => 'Unauthorized',
             ]);
}
}
