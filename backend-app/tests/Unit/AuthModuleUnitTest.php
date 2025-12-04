<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthModuleUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_genera_password_hasheado_y_verificado()
    {
        $user = User::factory()->create();

        $this->assertTrue(Hash::check('password', $user->password), 'La clave de la fabrica debe venir hasheada');
        $this->assertNotNull($user->email_verified_at, 'El usuario debe quedar verificado por defecto');
    }
}
