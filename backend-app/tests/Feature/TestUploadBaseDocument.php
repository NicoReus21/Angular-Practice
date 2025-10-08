<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Process;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class TestUploadBaseDocument extends TestCase{

    use RefreshDatabase;
    protected Process $process;
    protected User $user;
    protected function setUp(): void{
        parent::setUp();
        $this->user = User::factory()->create([
            "email"=> "test_process@example.com",
            "password"=> bcrypt("password"),
        ]);
        $this->actingAs($this->user);
        $this->process = Process::factory()->create([
            'bombero_name' => 'Juan Pérez',
            'company' => 'Compañía Central',
            'user_id'=> $this->user->id,
        ]);
   }
    protected function upload_file(Process $process,String $section_title,String $route_name,String $step,String $message){
        Storage::fake('local');
        // Creamos un archivo fake
        $document = UploadedFile::fake()->create($section_title.'.pdf', 200, 'application/pdf');

        // Enviamos la request al endpoint de subida de reportes
        $response = $this->postJson(route($route_name, [
            'process' => $this->process->id
        ]), [
            'document' => $document,
        ]);
        
        // Obtener el mensaje de error si la respuesta no es exitosa
        // Verificamos que el endpoint responda correctamente
        $response->assertStatus(201)
             ->assertJson([
                 'message' => $message
        ]);
        $filename = $response->json('path');
        // Verificamos que el archivo se haya almacenado
        // Verificamos que el archivo se haya almacenado en el storage fake
        $realPath = Storage::disk('local')->path('private/' . $filename);
        Storage::disk('local')->assertExists('' . $filename);

        $firstDocument = \App\Models\Document::first();
        $this->assertDatabaseHas('documents', [
            'process_id' => $this->process->id,
            'section_title' => $section_title,           // columna correcta
            'step' => $step,         // opcional, según quieras verificar
            'file_name' => $response->json('document')['file_name'],                     // columna correcta
]       );
    }
}
