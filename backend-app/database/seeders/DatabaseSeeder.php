<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\UserGroup;
use Database\Seeders\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        $group = Group::firstOrCreate(
            ['name' => 'System group'],
            [
                'description' => 'Grupo de soporte del sistema',
                'id_parent_group' => null,
                'id_user_created' => $user->id,
            ]
        );

        UserGroup::updateOrCreate(
            [
                'id_user' => $user->id,
                'id_group' => $group->id,
            ],
            [
                'assigned_at' => now(),
                'removed_at' => null,
                'id_user_created' => $user->id,
            ]
        );

        $this->call(PermissionSeeder::class);
    }
}
