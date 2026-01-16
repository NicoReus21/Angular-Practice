<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Http\Request;

trait ChecksCompanyPermission
{
    protected function forbidIfNoCompanyPermission(Request $request, Company $company, string $action, string $section = 'Car')
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $companyKey = $company->permissionKey();
        if (!$user->hasCompanyPermission('Material Mayor', $section, $action, $companyKey)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return null;
    }
}
