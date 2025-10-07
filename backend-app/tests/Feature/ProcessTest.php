<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProcessTest extends TestCase
{
use RefreshDatabase;    

    public function test_store(){
        $response = $this->postJson(route('process.store'), [
            'bombero_nombre'=> 'test bombero',
            'compania'=> 'test compañia',
        ]);
        $response->assertStatus(201);
    }
}
