<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MaintenanceDocument;

class Maintenance extends Model
{
    use HasFactory;

    protected $with = ['documents'];

    protected $fillable = [
        'car_id',
        'vendor_id',
        'service_date',
        'chassis_number',
        'mileage',
        'cabin',
        'filter_code',
        'hourmeter',
        'warnings',
        'location',
        'service_type',
        'inspector_name',
        'reported_problem',
        'activities_detail',
        'pending_work',
        'pending_type',
        'observations',
        'car_info_annex',
        'inspector_signature',
        'officer_signature',
        'status',
        'pdf_url',
        'manufacturing_year',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }


    public function documents()
    {
        return $this->hasMany(MaintenanceDocument::class, 'maintenance_id');
    }
}
