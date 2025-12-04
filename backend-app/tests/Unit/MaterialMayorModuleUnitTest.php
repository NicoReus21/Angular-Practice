<?php

namespace Tests\Unit;

use App\Models\Car;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialMayorModuleUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_crea_unidad_operativa_con_placa()
    {
        $car = Car::factory()->create();

        $this->assertEquals('operativo', $car->status, 'Las unidades nuevas deben partir operativas');
        $this->assertNotEmpty($car->plate, 'La placa es obligatoria para identificar la unidad');
    }

    public function test_relaciones_has_many_para_mantenimientos()
    {
        $car = Car::factory()->make();

        $this->assertInstanceOf(HasMany::class, $car->maintenances(), 'El carro debe exponer la relacion de mantenimientos');
    }
}
