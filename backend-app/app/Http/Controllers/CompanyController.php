<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

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
        $company = Company::create($data);
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
        $company->update($data);
        return $company;
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->noContent();
    }
}

