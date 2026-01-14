<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorReportLink extends Model
{
    /** @use HasFactory<\Database\Factories\VendorReportLinkFactory> */
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'car_id',
        'token',
        'expires_at',
        'used_at',
        'maintenance_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
