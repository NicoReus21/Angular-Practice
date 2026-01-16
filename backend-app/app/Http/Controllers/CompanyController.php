<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index()
    {
        return Company::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);
        if (empty($data['code'])) {
            $data['code'] = Str::slug($data['name'], '-');
        }
        $company = Company::create($data);
        $this->ensureCompanyPermissions($company);
        return response()->json($company, 201);
    }

    public function show(Company $company)
    {
        return $company;
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);
        if (array_key_exists('name', $data) && empty($data['code'])) {
            $data['code'] = Str::slug($data['name'], '-');
        }
        $company->update($data);
        $this->ensureCompanyPermissions($company);
        return $company;
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->noContent();
    }

    private function ensureCompanyPermissions(Company $company): void
    {
        $permissionKey = $company->permissionKey();
        if (!$company->code) {
            $company->update(['code' => $permissionKey]);
        }

        $actions = ['create', 'read', 'update', 'delete'];
        $sections = ['Car', 'Maintenance', 'Document', 'Checklist'];

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
