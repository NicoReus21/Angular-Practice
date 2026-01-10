<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_se_crea_un_proceso()
    {
        $payload = [
            'bombero_name' => 'test bombero',
            'company' => 'test compania',
        ];

        $user = User::factory()->create();
        $this->grantPermission($user, 'Bombero Accidentado', 'Process', 'create');
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson(route('process.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'bombero_name',
                'company',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('processes', [
            'bombero_name' => 'test bombero',
            'company' => 'test compania',
        ]);
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
