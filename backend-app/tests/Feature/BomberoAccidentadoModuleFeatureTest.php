<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BomberoAccidentadoModuleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_puede_crear_proceso_operativo()
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'Bombero Accidentado', 'Process', 'create');
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
        $this->grantPermission($user, 'Sistema', 'Permission', 'read');

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

    private function grantPermission(User $user, string $module, string $section, string $action): void
    {
        $permission = Permission::updateOrCreate(
            [
                'module' => $module,
                'section' => $section,
                'action' => $action,
            ],
            [
                'description' => "{$action} {$section}",
            ]
        );

        DB::table('user_permissions')->updateOrInsert(
            [
                'id_user' => $user->id,
                'id_permission' => $permission->id,
            ],
            [
                'granted_at' => Carbon::today()->toDateString(),
                'revoked_at' => null,
                'id_user_created' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
