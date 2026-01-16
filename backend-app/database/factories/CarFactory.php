<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyName = $this->faker->company();
        $company = Company::firstOrCreate(
            ['name' => $companyName],
            ['code' => Str::slug($companyName, '-')]
        );

        return [
            'name' => $this->faker->company() . ' Unidad',
            'plate' => strtoupper($this->faker->bothify('??-####')),
            'model' => $this->faker->word(),
            'company' => $company->name,
            'company_id' => $company->id,
            'status' => 'operativo',
            'imageUrl' => null,
            'marca' => $this->faker->company(),
            'chassis_number' => $this->faker->bothify('CH-####'),
            'type' => $this->faker->randomElement(['Camion', 'Rescate', 'Ambulancia']),
            'cabin' => $this->faker->randomElement(['Simple', 'Doble']),
            'mileage' => $this->faker->numberBetween(0, 150000),
            'hourmeter' => $this->faker->numberBetween(0, 8000),
        ];
    }
}
