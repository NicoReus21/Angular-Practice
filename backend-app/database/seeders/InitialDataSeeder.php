<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupPermission;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure base permissions exist before linking.
        $this->call(PermissionSeeder::class);

        $today = Carbon::today()->toDateString();

        $systemUser = User::updateOrCreate(
            ['email' => 'system@sigba.test'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
            ]
        );

        $adminUser = User::updateOrCreate(
            ['email' => 'encargadoadministracion@gmail.com'],
            [
                'name' => 'Encargado Administracion',
                'password' => bcrypt('password'),
            ]
        );

        $mmUser = User::updateOrCreate(
            ['email' => 'encargadomaterialmayor@gmail.com'],
            [
                'name' => 'Encargado Material Mayor',
                'password' => bcrypt('password'),
            ]
        );

        $baUser = User::updateOrCreate(
            ['email' => 'encargadobomberoaccidentado@gmail.com'],
            [
                'name' => 'Encargado Bombero Accidentado',
                'password' => bcrypt('password'),
            ]
        );

        $systemGroup = Group::updateOrCreate(
            ['name' => 'System'],
            [
                'description' => 'Grupo con todos los permisos',
                'id_parent_group' => null,
                'id_user_created' => $systemUser->id,
            ]
        );

        $adminGroup = Group::updateOrCreate(
            ['name' => 'Administracion'],
            [
                'description' => 'Gestion de usuarios, grupos y permisos',
                'id_parent_group' => $systemGroup->id,
                'id_user_created' => $systemUser->id,
            ]
        );

        $mmGroup = Group::updateOrCreate(
            ['name' => 'Material Mayor'],
            [
                'description' => 'Permisos de Material Mayor',
                'id_parent_group' => $adminGroup->id,
                'id_user_created' => $systemUser->id,
            ]
        );

        $baGroup = Group::updateOrCreate(
            ['name' => 'Bombero Accidentado'],
            [
                'description' => 'Permisos de Bombero Accidentado',
                'id_parent_group' => $adminGroup->id,
                'id_user_created' => $systemUser->id,
            ]
        );

        $guestGroup = Group::updateOrCreate(
            ['name' => 'Invitados'],
            [
                'description' => 'Acceso limitado para usuarios externos',
                'id_parent_group' => null,
                'id_user_created' => $systemUser->id,
            ]
        );

        UserGroup::updateOrCreate(
            ['id_user' => $systemUser->id, 'id_group' => $systemGroup->id],
            [
                'assigned_at' => $today,
                'removed_at' => null,
                'id_user_created' => $systemUser->id,
            ]
        );

        UserGroup::updateOrCreate(
            ['id_user' => $adminUser->id, 'id_group' => $adminGroup->id],
            [
                'assigned_at' => $today,
                'removed_at' => null,
                'id_user_created' => $systemUser->id,
            ]
        );

        UserGroup::updateOrCreate(
            ['id_user' => $mmUser->id, 'id_group' => $mmGroup->id],
            [
                'assigned_at' => $today,
                'removed_at' => null,
                'id_user_created' => $systemUser->id,
            ]
        );

        UserGroup::updateOrCreate(
            ['id_user' => $baUser->id, 'id_group' => $baGroup->id],
            [
                'assigned_at' => $today,
                'removed_at' => null,
                'id_user_created' => $systemUser->id,
            ]
        );

        $allPermissions = Permission::all();
        foreach ($allPermissions as $permission) {
            GroupPermission::updateOrCreate(
                ['id_group' => $systemGroup->id, 'id_permission' => $permission->id],
                [
                    'granted_at' => $today,
                    'revoked_at' => null,
                    'id_user_created' => $systemUser->id,
                ]
            );
        }

        $adminPermissions = Permission::where('module', 'Sistema')->get();
        foreach ($adminPermissions as $permission) {
            GroupPermission::updateOrCreate(
                ['id_group' => $adminGroup->id, 'id_permission' => $permission->id],
                [
                    'granted_at' => $today,
                    'revoked_at' => null,
                    'id_user_created' => $systemUser->id,
                ]
            );
        }

        $mmPermissions = Permission::where('module', 'Material Mayor')->get();
        foreach ($mmPermissions as $permission) {
            GroupPermission::updateOrCreate(
                ['id_group' => $mmGroup->id, 'id_permission' => $permission->id],
                [
                    'granted_at' => $today,
                    'revoked_at' => null,
                    'id_user_created' => $systemUser->id,
                ]
            );
        }

        $baPermissions = Permission::where('module', 'Bombero Accidentado')->get();
        foreach ($baPermissions as $permission) {
            GroupPermission::updateOrCreate(
                ['id_group' => $baGroup->id, 'id_permission' => $permission->id],
                [
                    'granted_at' => $today,
                    'revoked_at' => null,
                    'id_user_created' => $systemUser->id,
                ]
            );
        }
    }
}
