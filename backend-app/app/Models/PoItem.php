<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoItem extends Model
{
    use HasFactory;

    protected $table = 'po_items';
    protected $fillable = ['purchase_order_id','car_id','description','qty','unit_price','line_total'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}

