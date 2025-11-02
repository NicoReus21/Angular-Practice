<?php

namespace App\Http\Controllers;

use App\Models\PaymentMm;
use Illuminate\Http\Request;

class PaymentsMmController extends Controller
{
    public function index()
    {
        return PaymentMm::with('invoice')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices_mm,id',
            'paid_at' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'method' => 'nullable|string|max:100',
        ]);
        $payment = PaymentMm::create($data);
        return response()->json($payment->load('invoice'), 201);
    }

    public function show(PaymentMm $mm_payment)
    {
        return $mm_payment->load('invoice');
    }

    public function update(Request $request, PaymentMm $mm_payment)
    {
        $data = $request->validate([
            'paid_at' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'method' => 'nullable|string|max:100',
        ]);
        $mm_payment->update($data);
        return $mm_payment->load('invoice');
    }

    public function destroy(PaymentMm $mm_payment)
    {
        $mm_payment->delete();
        return response()->noContent();
    }
}

