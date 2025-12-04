<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Process>
 */
class ProcessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bombero_name' => $this->faker->name(),
            'company' => $this->faker->company(),
            'status' => 'draft',
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
