<?php

namespace App\Http\Controllers;

use App\Models\AnnualBudget; 
use Illuminate\Http\Request;

class AnnualBudgetController extends Controller
{
    public function show($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $budget = AnnualBudget::firstOrNew(['year' => $year], ['amount' => 0]);

        return response()->json($budget);
    }

    public function store(Request $request)
    {

        $request->validate([
            'year' => 'required|integer',
            'amount' => 'required|numeric'
        ]);

        $budget = AnnualBudget::updateOrCreate(
            ['year' => $request->year],
            ['amount' => $request->amount]
        );

        return response()->json($budget);
    }
}