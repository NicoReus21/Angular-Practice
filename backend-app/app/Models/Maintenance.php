<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;
    protected $fillable = [
        'car_id',
        'chassis_number',
        'mileage',
        'cabin',
        'status',
        'filter_code',
        'hourmeter',
        'warnings',
        'service_type',
        'inspector_name',
        'service_date',
        'location',
        'reported_problem',
        'activities_detail',
        'pending_work',
        'pending_type',
        'observations',
        'inspector_signature',
        'officer_signature',
        'car_info_annex',
    ];

    protected $casts = [
        'service_date' => 'date',
        'mileage' => 'integer',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}