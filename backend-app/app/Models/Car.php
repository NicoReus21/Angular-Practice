<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'plate',
        'model',
        'company',
        'company_id',
        'status',
        'imageUrl',
        'marca',
        'chassis_number',
        'type',
        'cabin',
        'mileage',
        'hourmeter',
    ];

    /**
     * Get the maintenances for the car.
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class)->orderBy('service_date', 'desc');
    }

    /**
     * Get the checklists for the car.
     */
    public function checklists()
    {
        return $this->hasMany(CarChecklist::class)->orderBy('fecha_realizacion', 'desc');
    }

    /**
     * Get the documents for the car.
     */
    public function documents()
    {
        return $this->hasMany(CarDocument::class)->orderBy('created_at', 'desc');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
