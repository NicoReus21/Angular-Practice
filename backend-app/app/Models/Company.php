<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name','code'];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function permissionKey(): string
    {
        $value = $this->code ?: $this->name;
        return Str::of($value)->lower()->slug('-');
    }
}
