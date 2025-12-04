<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $modules = ['Bombero Accidentado', 'Material Mayor', 'Autenticacion'];
        $actions = ['create', 'read', 'update', 'delete'];

        return [
            'module' => $this->faker->randomElement($modules),
            'section' => $this->faker->randomElement(['process', 'car', 'document', 'user']),
            'action' => $this->faker->randomElement($actions),
            'description' => $this->faker->sentence(),
        ];
    }
}
