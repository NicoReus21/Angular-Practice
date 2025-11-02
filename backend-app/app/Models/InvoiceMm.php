<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceMm extends Model
{
    use HasFactory;

    protected $table = 'invoices_mm';
    protected $fillable = [
        'supplier_id','company_id','purchase_order_id','maintenance_id',
        'issue_date','due_date','net','tax','total','alert_sent_at'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'net' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'alert_sent_at' => 'datetime',
    ];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function maintenance() { return $this->belongsTo(Maintenance::class); }
    public function payments() { return $this->hasMany(PaymentMm::class, 'invoice_id'); }
}

