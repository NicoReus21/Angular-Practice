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
    ];
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
