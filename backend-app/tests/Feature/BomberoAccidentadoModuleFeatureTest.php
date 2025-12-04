<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BomberoAccidentadoModuleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_puede_crear_proceso_operativo()
    {
        $user = User::factory()->create();
        $payload = [
            'bombero_name' => 'Juan Perez',
            'company' => 'Primera Cia',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/process', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'bombero_name' => 'Juan Perez',
                'company' => 'Primera Cia',
            ]);
    }

    public function test_ruta_de_permisos_filtra_por_modulo_bombero_accidentado()
    {
        $user = User::factory()->create();

        Permission::factory()->create([
            'module' => 'Bombero Accidentado',
            'section' => 'process',
            'action' => 'create',
        ]);
        Permission::factory()->create([
            'module' => 'Material Mayor',
            'section' => 'car',
            'action' => 'read',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/modules/bombero-accidentado/permissions');

        $response->assertStatus(200)
            ->assertJsonFragment(['module' => 'Bombero Accidentado'])
            ->assertJsonMissing(['module' => 'Material Mayor'])
            ->assertJsonCount(1);
    }
}
