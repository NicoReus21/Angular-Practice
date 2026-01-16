<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class CompanyPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $actions = ['create', 'read', 'update', 'delete'];
        $sections = ['Car', 'Maintenance', 'Document', 'Checklist'];

        foreach ($companies as $company) {
            $permissionKey = $company->permissionKey();
            if (!$company->code) {
                $company->update(['code' => $permissionKey]);
            }

            foreach ($sections as $section) {
                foreach ($actions as $action) {
                    Permission::updateOrCreate(
                        [
                            'module' => 'Material Mayor',
                            'section' => "{$section}:{$permissionKey}",
                            'action' => $action,
                        ],
                        [
                            'description' => "{$action} {$section} {$permissionKey}",
                        ]
                    );
                }
            }
        }
    }
}
