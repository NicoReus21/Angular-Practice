<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        return Budget::with('company')->orderByDesc('year')->orderByDesc('month')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
        ]);
        $budget = Budget::create($data);
        return response()->json($budget->load('company'), 201);
    }

    public function show(Budget $budget)
    {
        return $budget->load('company');
    }

    public function update(Request $request, Budget $budget)
    {
        $data = $request->validate([
            'company_id' => 'sometimes|required|exists:companies,id',
            'year' => 'sometimes|required|integer|min:2000|max:2100',
            'month' => 'sometimes|required|integer|min:1|max:12',
            'amount' => 'sometimes|required|numeric|min:0',
        ]);
        $budget->update($data);
        return $budget->load('company');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return response()->noContent();
    }
}

