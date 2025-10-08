<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ProcessTest extends TestCase
{
    use RefreshDatabase;    


    public function test_se_crea_un_proceso()
    {
        $payload = [
            'bombero_name' => 'test bombero',
            'company' => 'test compañia',
        ];
         $user = User::factory()->create();
        $this->actingAs($user);
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
            'company' => 'test compañia',
        ]);
    }
}