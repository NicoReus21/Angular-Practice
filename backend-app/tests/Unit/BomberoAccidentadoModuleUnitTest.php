<?php

namespace Tests\Unit;

use App\Models\Process;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BomberoAccidentadoModuleUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_crea_proceso_asociado_a_un_usuario()
    {
        $process = Process::factory()->create();

        $this->assertNotNull($process->user_id, 'El proceso debe vincularse a un usuario responsable');
        $this->assertEquals('draft', $process->status, 'El estado inicial del proceso debe ser draft');
    }

    public function test_relacion_documentos_es_has_many()
    {
        $process = Process::factory()->make();

        $this->assertInstanceOf(HasMany::class, $process->documents(), 'Un proceso debe exponer relacion hasMany con documentos');
    }
}
