<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $definitions = [
            // Operativos generales
            ['module' => 'Bombero Accidentado', 'section' => 'Process', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Bombero Accidentado', 'section' => 'Home', 'actions' => ['read']],

            // Material Mayor
            ['module' => 'Material Mayor', 'section' => 'Car', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Car:all', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Maintenance', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Maintenance:all', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Document', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Document:all', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Checklist', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Checklist:all', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Inspection', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Inspection:all', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'InspectionCategory', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'AnnualBudget', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Material Mayor', 'section' => 'Home', 'actions' => ['read']],

            // Administración
            ['module' => 'Sistema', 'section' => 'User', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Sistema', 'section' => 'Group', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Sistema', 'section' => 'Rol', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Sistema', 'section' => 'Permission', 'actions' => ['create', 'read', 'update', 'delete']],
            ['module' => 'Sistema', 'section' => 'Home', 'actions' => ['read']],
            ['module' => 'Sistema', 'section' => 'Modules', 'actions' => ['read']],
        ];

        foreach ($definitions as $def) {
            foreach ($def['actions'] as $action) {
                Permission::updateOrCreate(
                    [
                        'module' => $def['module'],
                        'section' => $def['section'],
                        'action' => $action,
                    ],
                    [
                        'description' => "{$action} {$def['section']}",
                    ]
                );
            }
        }
    }
}
