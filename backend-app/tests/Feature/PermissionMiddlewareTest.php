<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\CarChecklist;
use App\Models\CarChecklistItems;
use App\Models\CarDocument;
use App\Models\Document;
use App\Models\Group;
use App\Models\GroupPermission;
use App\Models\Maintenance;
use App\Models\Permission;
use App\Models\Process;
use App\Models\Rol;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserRol;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        Storage::fake('local');
        Storage::fake('public');

        $this->user = User::factory()->create();
    }

    /**
     * @dataProvider permissionCases
     */
    public function test_rutas_bloquean_sin_permiso(array $case): void
    {
        $this->actingAs($this->user, 'sanctum');

        $case = $this->prepareCase($case);
        $response = $this->callRequest($case);

        $response->assertStatus(403);
    }

    /**
     * @dataProvider permissionCases
     */
    public function test_rutas_permiten_con_permiso(array $case): void
    {
        $this->actingAs($this->user, 'sanctum');

        $case = $this->prepareCase($case);
        $this->grantPermission($case['permission']);

        $response = $this->callRequest($case);
        $response->assertStatus($case['expected_status']);
    }

    public static function permissionCases(): array
    {
        $cases = [
            'process_index' => [
                'method' => 'GET',
                'uri' => '/api/process',
                'permission' => 'Bombero Accidentado:Process:read',
                'expected_status' => 200,
                'setup' => function (self $test): void {
                    $test->createProcess();
                },
            ],
            'process_store' => [
                'method' => 'POST',
                'uri' => '/api/process',
                'permission' => 'Bombero Accidentado:Process:create',
                'expected_status' => 201,
                'payload' => [
                    'bombero_name' => 'Test Bombero',
                    'company' => 'Test Company',
                ],
            ],
            'process_show' => [
                'method' => 'GET',
                'permission' => 'Bombero Accidentado:Process:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $process = $test->createProcess();
                    return ['uri' => "/api/process/{$process->id}"];
                },
            ],
            'process_update' => [
                'method' => 'PUT',
                'permission' => 'Bombero Accidentado:Process:update',
                'expected_status' => 200,
                'payload' => [
                    'bombero_name' => 'Updated Bombero',
                ],
                'setup' => function (self $test): array {
                    $process = $test->createProcess();
                    return ['uri' => "/api/process/{$process->id}"];
                },
            ],
            'process_finalize' => [
                'method' => 'PATCH',
                'permission' => 'Bombero Accidentado:Process:update',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $process = $test->createProcess();
                    return ['uri' => "/api/process/{$process->id}/finalize"];
                },
            ],
            'process_complete_step' => [
                'method' => 'PATCH',
                'permission' => 'Bombero Accidentado:Process:update',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $process = $test->createProcess();
                    return ['uri' => "/api/processes/{$process->id}/complete-step"];
                },
            ],
            'process_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Bombero Accidentado:Process:delete',
                'expected_status' => 204,
                'setup' => function (self $test): array {
                    $process = $test->createProcess();
                    return ['uri' => "/api/process/{$process->id}"];
                },
            ],
            'documents_view' => [
                'method' => 'GET',
                'permission' => 'Bombero Accidentado:Process:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $document = $test->createProcessDocument();
                    return ['uri' => "/api/documents/{$document->id}/view"];
                },
            ],
            'documents_download' => [
                'method' => 'GET',
                'permission' => 'Bombero Accidentado:Process:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $document = $test->createProcessDocument();
                    return ['uri' => "/api/documents/{$document->id}/download"];
                },
            ],
            'documents_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Material Mayor:Document:delete',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $document = $test->createProcessDocument();
                    return ['uri' => "/api/documents/{$document->id}"];
                },
            ],
            'users_index' => [
                'method' => 'GET',
                'uri' => '/api/users',
                'permission' => 'Sistema:User:read',
                'expected_status' => 200,
                'setup' => function (self $test): void {
                    User::factory()->count(2)->create();
                },
            ],
            'users_store' => [
                'method' => 'POST',
                'uri' => '/api/users',
                'permission' => 'Sistema:User:create',
                'expected_status' => 201,
                'payload' => [
                    'name' => 'Nuevo Usuario',
                    'email' => 'nuevo_usuario@test.com',
                    'password' => 'password123',
                ],
            ],
            'users_show' => [
                'method' => 'GET',
                'permission' => 'Sistema:User:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    return ['uri' => "/api/users/{$user->id}"];
                },
            ],
            'users_update' => [
                'method' => 'PUT',
                'permission' => 'Sistema:User:update',
                'expected_status' => 200,
                'payload' => [
                    'name' => 'Usuario Actualizado',
                ],
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    return ['uri' => "/api/users/{$user->id}"];
                },
            ],
            'users_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Sistema:User:delete',
                'expected_status' => 204,
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    return ['uri' => "/api/users/{$user->id}"];
                },
            ],
            'user_roles_list' => [
                'method' => 'GET',
                'permission' => 'Sistema:User:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    return ['uri' => "/api/users/{$user->id}/roles"];
                },
            ],
            'user_roles_assign' => [
                'method' => 'POST',
                'permission' => 'Sistema:User:update',
                'expected_status' => 201,
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    $rol = $test->createRol();
                    return ['uri' => "/api/users/{$user->id}/roles/{$rol->id}"];
                },
            ],
            'user_roles_remove' => [
                'method' => 'DELETE',
                'permission' => 'Sistema:User:update',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    $rol = $test->createRol();
                    UserRol::create([
                        'id_user' => $user->id,
                        'id_rol' => $rol->id,
                        'assigned_at' => Carbon::today()->toDateString(),
                        'removed_at' => null,
                        'id_user_created' => $test->user->id,
                    ]);
                    return ['uri' => "/api/users/{$user->id}/roles/{$rol->id}"];
                },
            ],
            'user_rols_store_validation' => [
                'method' => 'POST',
                'uri' => '/api/user-rols',
                'permission' => 'Sistema:User:update',
                'expected_status' => 422,
                'payload' => [],
            ],
            'user_rols_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Sistema:User:update',
                'expected_status' => 204,
                'setup' => function (self $test): array {
                    $rol = $test->createRol();
                    $assignment = UserRol::create([
                        'id_user' => $test->user->id,
                        'id_rol' => $rol->id,
                        'assigned_at' => Carbon::today()->toDateString(),
                        'removed_at' => null,
                        'id_user_created' => $test->user->id,
                    ]);
                    return ['uri' => "/api/user-rols/{$assignment->id}"];
                },
            ],
            'user_groups_list' => [
                'method' => 'GET',
                'permission' => 'Sistema:User:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    return ['uri' => "/api/users/{$user->id}/groups"];
                },
            ],
            'group_users_list' => [
                'method' => 'GET',
                'permission' => 'Sistema:Group:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $group = $test->createGroup();
                    return ['uri' => "/api/groups/{$group->id}/users"];
                },
            ],
            'user_groups_assign' => [
                'method' => 'POST',
                'permission' => 'Sistema:User:update',
                'expected_status' => 201,
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    $group = $test->createGroup();
                    return ['uri' => "/api/users/{$user->id}/groups/{$group->id}"];
                },
            ],
            'user_groups_remove' => [
                'method' => 'DELETE',
                'permission' => 'Sistema:User:update',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $user = User::factory()->create();
                    $group = $test->createGroup();
                    UserGroup::create([
                        'id_user' => $user->id,
                        'id_group' => $group->id,
                        'assigned_at' => Carbon::today()->toDateString(),
                        'removed_at' => null,
                        'id_user_created' => $test->user->id,
                    ]);
                    return ['uri' => "/api/users/{$user->id}/groups/{$group->id}"];
                },
            ],
            'group_permissions_assign' => [
                'method' => 'POST',
                'permission' => 'Sistema:Group:update',
                'expected_status' => 201,
                'setup' => function (self $test): array {
                    $group = $test->createGroup();
                    $permission = $test->createPermission('Sistema', 'Group', 'read');
                    return ['uri' => "/api/groups/{$group->id}/permissions/{$permission->id}"];
                },
            ],
            'group_permissions_revoke' => [
                'method' => 'DELETE',
                'permission' => 'Sistema:Group:update',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $group = $test->createGroup();
                    $permission = $test->createPermission('Sistema', 'Group', 'read');
                    GroupPermission::create([
                        'id_group' => $group->id,
                        'id_permission' => $permission->id,
                        'granted_at' => Carbon::today()->toDateString(),
                        'revoked_at' => null,
                        'id_user_created' => $test->user->id,
                    ]);
                    return ['uri' => "/api/groups/{$group->id}/permissions/{$permission->id}"];
                },
            ],
            'groups_index' => [
                'method' => 'GET',
                'uri' => '/api/groups',
                'permission' => 'Sistema:Group:read',
                'expected_status' => 200,
                'setup' => function (self $test): void {
                    $test->createGroup();
                },
            ],
            'groups_show' => [
                'method' => 'GET',
                'permission' => 'Sistema:Group:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $group = $test->createGroup();
                    return ['uri' => "/api/groups/{$group->id}"];
                },
            ],
            'groups_store' => [
                'method' => 'POST',
                'uri' => '/api/groups',
                'permission' => 'Sistema:Group:create',
                'expected_status' => 201,
                'payload' => [
                    'name' => 'Grupo Test',
                    'description' => 'Grupo de prueba',
                ],
            ],
            'groups_update' => [
                'method' => 'PUT',
                'permission' => 'Sistema:Group:update',
                'expected_status' => 200,
                'payload' => [
                    'name' => 'Grupo Actualizado',
                ],
                'setup' => function (self $test): array {
                    $group = $test->createGroup();
                    return ['uri' => "/api/groups/{$group->id}"];
                },
            ],
            'groups_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Sistema:Group:delete',
                'expected_status' => 204,
                'setup' => function (self $test): array {
                    $group = $test->createGroup();
                    return ['uri' => "/api/groups/{$group->id}"];
                },
            ],
            'rols_index' => [
                'method' => 'GET',
                'uri' => '/api/rols',
                'permission' => 'Sistema:Rol:read',
                'expected_status' => 200,
                'setup' => function (self $test): void {
                    $test->createRol();
                },
            ],
            'rols_show' => [
                'method' => 'GET',
                'permission' => 'Sistema:Rol:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $rol = $test->createRol();
                    return ['uri' => "/api/rols/{$rol->id}"];
                },
            ],
            'rols_store' => [
                'method' => 'POST',
                'uri' => '/api/rols',
                'permission' => 'Sistema:Rol:create',
                'expected_status' => 201,
                'payload' => [
                    'name' => 'Rol de prueba',
                    'description' => 'Rol generado para test',
                ],
            ],
            'rols_update' => [
                'method' => 'PUT',
                'permission' => 'Sistema:Rol:update',
                'expected_status' => 200,
                'payload' => [
                    'name' => 'Rol actualizado',
                ],
                'setup' => function (self $test): array {
                    $rol = $test->createRol();
                    return ['uri' => "/api/rols/{$rol->id}"];
                },
            ],
            'rols_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Sistema:Rol:delete',
                'expected_status' => 204,
                'setup' => function (self $test): array {
                    $rol = $test->createRol();
                    return ['uri' => "/api/rols/{$rol->id}"];
                },
            ],
            'rols_permissions_list' => [
                'method' => 'GET',
                'permission' => 'Sistema:Rol:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $rol = $test->createRol();
                    return ['uri' => "/api/rols/{$rol->id}/permissions"];
                },
            ],
            'rols_permissions_sync' => [
                'method' => 'POST',
                'permission' => 'Sistema:Rol:update',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $rol = $test->createRol();
                    $permission = $test->createPermission('Sistema', 'Rol', 'read');
                    return [
                        'uri' => "/api/rols/{$rol->id}/permissions",
                        'payload' => [
                            'permissions' => [$permission->id],
                        ],
                    ];
                },
            ],
            'permissions_index' => [
                'method' => 'GET',
                'uri' => '/api/permissions',
                'permission' => 'Sistema:Permission:read',
                'expected_status' => 200,
            ],
            'permissions_bombero_accidentado' => [
                'method' => 'GET',
                'uri' => '/api/modules/bombero-accidentado/permissions',
                'permission' => 'Sistema:Permission:read',
                'expected_status' => 200,
            ],
            'permissions_material_mayor' => [
                'method' => 'GET',
                'uri' => '/api/modules/material-mayor/permissions',
                'permission' => 'Sistema:Permission:read',
                'expected_status' => 200,
            ],
            'cars_index' => [
                'method' => 'GET',
                'uri' => '/api/cars',
                'permission' => 'Material Mayor:Car:read',
                'expected_status' => 200,
                'setup' => function (self $test): void {
                    Car::factory()->create();
                },
            ],
            'cars_show' => [
                'method' => 'GET',
                'permission' => 'Material Mayor:Car:read',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    return ['uri' => "/api/cars/{$car->id}"];
                },
            ],
            'cars_store' => [
                'method' => 'POST',
                'uri' => '/api/cars',
                'permission' => 'Material Mayor:Car:create',
                'expected_status' => 201,
                'payload' => [
                    'name' => 'Unidad Test',
                    'plate' => 'TT-1234',
                    'company' => 'Compania Test',
                    'status' => 'Operativo',
                ],
            ],
            'cars_update' => [
                'method' => 'PUT',
                'permission' => 'Material Mayor:Car:update',
                'expected_status' => 200,
                'payload' => [
                    'name' => 'Unidad Actualizada',
                ],
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    return ['uri' => "/api/cars/{$car->id}"];
                },
            ],
            'cars_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Material Mayor:Car:delete',
                'expected_status' => 204,
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    return ['uri' => "/api/cars/{$car->id}"];
                },
            ],
            'maintenances_store' => [
                'method' => 'POST',
                'permission' => 'Material Mayor:Maintenance:create',
                'expected_status' => 201,
                'payload' => [
                    'status' => 'draft',
                    'service_date' => '2025-01-01',
                ],
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    return ['uri' => "/api/cars/{$car->id}/maintenances"];
                },
            ],
            'maintenances_update' => [
                'method' => 'PUT',
                'permission' => 'Material Mayor:Maintenance:update',
                'expected_status' => 200,
                'payload' => [
                    'status' => 'draft',
                    'service_date' => '2025-01-02',
                ],
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    $maintenance = Maintenance::create([
                        'car_id' => $car->id,
                        'status' => 'draft',
                        'service_date' => '2025-01-01',
                    ]);
                    return ['uri' => "/api/maintenances/{$maintenance->id}"];
                },
            ],
            'maintenances_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Material Mayor:Maintenance:delete',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    $maintenance = Maintenance::create([
                        'car_id' => $car->id,
                        'status' => 'draft',
                        'service_date' => '2025-01-01',
                    ]);
                    return ['uri' => "/api/maintenances/{$maintenance->id}"];
                },
            ],
            'checklists_store' => [
                'method' => 'POST',
                'permission' => 'Material Mayor:Checklist:create',
                'expected_status' => 201,
                'payload' => [
                    'persona_cargo' => 'Inspector Test',
                    'fecha_realizacion' => '2025-01-03',
                    'tasks' => ['Revision general'],
                ],
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    return ['uri' => "/api/cars/{$car->id}/checklists"];
                },
            ],
            'checklists_update' => [
                'method' => 'PUT',
                'permission' => 'Material Mayor:Checklist:update',
                'expected_status' => 200,
                'payload' => [
                    'persona_cargo' => 'Inspector Actualizado',
                    'fecha_realizacion' => '2025-01-04',
                    'tasks' => ['Revision completa'],
                ],
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    $checklist = CarChecklist::create([
                        'car_id' => $car->id,
                        'persona_cargo' => 'Inspector',
                        'fecha_realizacion' => '2025-01-01',
                    ]);
                    $checklist->items()->create([
                        'task_description' => 'Inicial',
                        'completed' => false,
                    ]);
                    return ['uri' => "/api/checklists/{$checklist->id}"];
                },
            ],
            'checklists_destroy' => [
                'method' => 'DELETE',
                'permission' => 'Material Mayor:Checklist:delete',
                'expected_status' => 204,
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    $checklist = CarChecklist::create([
                        'car_id' => $car->id,
                        'persona_cargo' => 'Inspector',
                        'fecha_realizacion' => '2025-01-01',
                    ]);
                    $checklist->items()->create([
                        'task_description' => 'Inicial',
                        'completed' => false,
                    ]);
                    return ['uri' => "/api/checklists/{$checklist->id}"];
                },
            ],
            'checklist_items_toggle' => [
                'method' => 'PATCH',
                'permission' => 'Material Mayor:Checklist:update',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    $checklist = CarChecklist::create([
                        'car_id' => $car->id,
                        'persona_cargo' => 'Inspector',
                        'fecha_realizacion' => '2025-01-01',
                    ]);
                    $item = CarChecklistItems::create([
                        'checklist_id' => $checklist->id,
                        'task_description' => 'Inicial',
                        'completed' => false,
                    ]);
                    return ['uri' => "/api/checklist-items/{$item->id}/toggle"];
                },
            ],
            'car_documents_store' => [
                'method' => 'POST',
                'permission' => 'Material Mayor:Document:create',
                'expected_status' => 201,
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    return [
                        'uri' => "/api/cars/{$car->id}/documents",
                        'payload' => [
                            'cost' => 1500,
                        ],
                        'files' => [
                            'file' => UploadedFile::fake()->create('doc.pdf', 50, 'application/pdf'),
                        ],
                    ];
                },
            ],
            'car_documents_toggle_payment' => [
                'method' => 'PATCH',
                'permission' => 'Material Mayor:Document:update',
                'expected_status' => 200,
                'setup' => function (self $test): array {
                    $car = Car::factory()->create();
                    $document = CarDocument::create([
                        'car_id' => $car->id,
                        'cost' => 2000,
                        'file_name' => 'archivo.pdf',
                        'path' => 'documents/archivo.pdf',
                        'file_type' => 'pdf',
                        'is_paid' => false,
                    ]);
                    return ['uri' => "/api/documents/{$document->id}/toggle-payment"];
                },
            ],
        ];

        $uploadRoutes = [
            'upload_reporte_flash',
            'upload_diab',
            'upload_obac',
            'upload_copia_libro_guardia',
            'upload_declaracion_testigo',
            'upload_certificado_carabineros',
            'upload_dau',
            'upload_informe_medico',
            'upload_otros_documento_medico_adicional',
            'upload_certificado_medico_atencion_especial',
            'upload_certificado_acreditacion_voluntario',
            'upload_copia_libro_llamada',
            'upload_aviso_citacion',
            'upload_copia_lista_asistencia',
            'upload_informe_ejecutivo',
            'upload_factura_prestaciones',
            'upload_boleta_honorarios_visada',
            'upload_boleta_medicamentos',
            'upload_certificado_medico_autorizacion_examen',
            'upload_boleta_factura_traslado',
            'upload_certificado_medico_traslado',
            'upload_boleta_gastos_acompanante',
            'upload_certificado_medico_incapacidad',
            'upload_boleta_alimentacion_acompanante',
            'upload_otros_gastos',
        ];

        foreach ($uploadRoutes as $route) {
            $cases["process_{$route}"] = [
                'method' => 'POST',
                'permission' => 'Bombero Accidentado:Process:update',
                'expected_status' => 201,
                'setup' => function (self $test) use ($route): array {
                    $process = $test->createProcess();
                    return [
                        'uri' => "/api/process/{$process->id}/{$route}",
                        'files' => [
                            'document' => UploadedFile::fake()->create('document.pdf', 20, 'application/pdf'),
                        ],
                    ];
                },
            ];
        }

        return $cases;
    }

    private function prepareCase(array $case): array
    {
        if (isset($case['setup']) && is_callable($case['setup'])) {
            $overrides = $case['setup']($this);
            if (is_array($overrides)) {
                $case = array_merge($case, $overrides);
            }
        }

        if (!isset($case['payload'])) {
            $case['payload'] = [];
        }

        if (!isset($case['files'])) {
            $case['files'] = [];
        }

        return $case;
    }

    private function callRequest(array $case)
    {
        $method = strtoupper($case['method']);
        $uri = $case['uri'];
        $payload = $case['payload'] ?? [];
        $files = $case['files'] ?? [];

        if (!empty($files)) {
            $payload = array_merge($payload, $files);
            return $this->call($method, $uri, $payload);
        }

        return $this->json($method, $uri, $payload);
    }

    private function grantPermission(string $permissionKey): void
    {
        [$module, $section, $action] = explode(':', $permissionKey);
        $permission = Permission::firstOrCreate(
            [
                'module' => $module,
                'section' => $section,
                'action' => $action,
            ],
            [
                'description' => $permissionKey,
            ]
        );

        DB::table('user_permissions')->insert([
            'id_user' => $this->user->id,
            'id_permission' => $permission->id,
            'granted_at' => Carbon::today()->toDateString(),
            'revoked_at' => null,
            'id_user_created' => $this->user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createProcess(): Process
    {
        return Process::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'Iniciado',
        ]);
    }

    private function createProcessDocument(): Document
    {
        $process = $this->createProcess();
        $filename = Str::uuid()->toString() . '.pdf';
        $path = "private/{$filename}";

        Storage::disk('local')->put($path, 'test');

        return Document::create([
            'process_id' => $process->id,
            'user_id' => $this->user->id,
            'file_name' => $filename,
            'file_path' => $path,
            'section_title' => 'reporte_flash',
            'step' => 'requerimiento_operativo',
        ]);
    }

    private function createGroup(): Group
    {
        return Group::create([
            'name' => 'Grupo ' . Str::uuid()->toString(),
            'description' => 'Grupo de prueba',
            'id_parent_group' => null,
            'id_user_created' => $this->user->id,
        ]);
    }

    private function createRol(): Rol
    {
        return Rol::create([
            'name' => 'Rol ' . Str::uuid()->toString(),
            'description' => 'Rol de prueba',
            'id_user_created' => $this->user->id,
        ]);
    }

    private function createPermission(string $module, string $section, string $action): Permission
    {
        return Permission::create([
            'module' => $module,
            'section' => $section,
            'action' => $action,
            'description' => "{$module}:{$section}:{$action}",
        ]);
    }
}
