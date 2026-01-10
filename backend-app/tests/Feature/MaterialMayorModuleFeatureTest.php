<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class MaterialMayorModuleFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_creacion_de_unidad_material_mayor()
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'Material Mayor', 'Car', 'create');

        $payload = [
            'name' => 'Unidad de Rescate',
            'plate' => 'RX-' . Str::upper(Str::random(4)),
            'model' => 'Modelo Test',
            'company' => 'Segunda Cia',
            'status' => 'operativo',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cars', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Unidad de Rescate',
                'plate' => $payload['plate'],
            ]);

        $this->assertDatabaseHas('cars', [
            'plate' => $payload['plate'],
            'company' => 'Segunda Cia',
        ]);
    }

    public function test_ruta_de_permisos_filtra_por_modulo_material_mayor()
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'Sistema', 'Permission', 'read');

        Permission::factory()->create([
            'module' => 'Material Mayor',
            'section' => 'car',
            'action' => 'create',
        ]);
        Permission::factory()->create([
            'module' => 'Bombero Accidentado',
            'section' => 'process',
            'action' => 'read',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/modules/material-mayor/permissions');

        $response->assertStatus(200)
            ->assertJsonFragment(['module' => 'Material Mayor'])
            ->assertJsonMissing(['module' => 'Bombero Accidentado'])
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
