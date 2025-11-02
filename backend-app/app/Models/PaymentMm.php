<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMm extends Model
{
    use HasFactory;

    protected $table = 'payments_mm';
    protected $fillable = ['invoice_id','paid_at','amount','method'];

    protected $casts = [
        'paid_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(InvoiceMm::class, 'invoice_id');
    }
}

