<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    /** @use HasFactory<\Database\Factories\CarFactory> */
    use HasFactory;
    protected $fillable = [
        'patente',
        'marca',
        'model',
        'company_id',
        'chassis_number',
        'type',
        'cabin',
        'mileage',
        'hourmeter',
        'status',
    ];
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function documents()
    {
        return $this->hasMany(CarDocument::class, 'car_id');
    }
}
