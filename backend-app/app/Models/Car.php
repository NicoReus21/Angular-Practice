<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// --- ESTAS SON LAS IMPORTACIONES QUE FALTABAN ANTES ---
use App\Models\Maintenance;
use App\Models\CarChecklist;
use App\Models\CarDocument;

class Car extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * (Esta es la lista que coincide con tu migración y tu controlador)
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
     * Un carro (unidad) tiene muchas mantenciones (reportes).
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    /**
     * Un carro tiene muchos checklists.
     * ESTA ES LA FUNCIÓN IMPORTANTE. 
     * hasMany(CarChecklist::class) buscará la tabla 'car_checklists'.
     */
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