<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Process;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TestUploadBaseDocument extends TestCase
{
    use RefreshDatabase;

    protected Process $process;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'test_process@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->grantPermission($this->user, 'Bombero Accidentado', 'Process', 'update');
        $this->actingAs($this->user, 'sanctum');

        $this->process = Process::factory()->create([
            'bombero_name' => 'Juan Perez',
            'company' => 'Compania Central',
            'user_id' => $this->user->id,
        ]);
    }

    protected function upload_file(Process $process, string $section_title, string $route_name, string $step, string $message): void
    {
        Storage::fake('local');

        $document = UploadedFile::fake()->create($section_title . '.pdf', 200, 'application/pdf');

        $response = $this->postJson(route($route_name, [
            'process' => $this->process->id,
        ]), [
            'document' => $document,
        ]);

        if ($response->status() === 500) {
            $this->fail('El endpoint devolvio 500: ' . $response->getContent());
        }

        $response->assertStatus(201)
            ->assertJson([
                'message' => $message,
            ]);

        $filePath = $response->json('document.file_path');
        $this->assertNotEmpty($filePath, 'La respuesta debe incluir la ruta del archivo generado');

        Storage::disk('local')->assertExists($filePath);

        $this->assertDatabaseHas('documents', [
            'process_id' => $this->process->id,
            'section_title' => $section_title,
            'step' => $step,
            'file_name' => $response->json('document.file_name'),
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
