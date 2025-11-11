<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance;
use App\Models\CarChecklist;
use App\Models\CarDocument;

class Car extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'plate',
        'model',
        'company',
        'status',
        'marca',
        'chassis_number',
        'type',
        'cabin',
        'mileage',
        'hourmeter',
    ];

    /**
     * Un carro de bombas unidad tiene muchos reportes o mantenciones.
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
    public function checklists()
    {
        return $this->hasMany(CarChecklist::class);
    }

    /**
     * Un carro tiene muchos documentos.
     */
    public function documents()
    {
        return $this->hasMany(CarDocument::class);
    }
}