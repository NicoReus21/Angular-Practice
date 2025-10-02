<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\UserGroup;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        Group::factory()->create([
            'name' => 'System group',
            'description' => 'Grupo de soporte del sistema',
            'id_parent_group' => null,
            'id_user_created' => 1,
        ]);
        UserGroup::factory()->create([
            'id_user' => 1,
            'id_group' => 1,
            'assigned_at' => now(),
            'removed_at' => null,
            'id_user_created' => 1,
        ]);
    }
}
