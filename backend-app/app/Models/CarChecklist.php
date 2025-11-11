<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Car;
use App\Models\CarChecklistItem;

class CarChecklist extends Model
{
    use HasFactory;

    protected $table = 'checklists';

    protected $fillable = [
        'car_id',
        'persona_cargo',
        'fecha_realizacion',
    ];

    protected $casts = [
        'fecha_realizacion' => 'date',
    ];

    /**
     * Un checklist pertenece a un Carro.
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Un checklist tiene muchas tareas items.
     */
    public function items()
    {
        return $this->hasMany(CarChecklistItem::class, 'checklist_id');
    }
}