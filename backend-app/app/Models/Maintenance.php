<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    /** @use HasFactory<\Database\Factories\MaintenanceFactory> */
    use HasFactory;
    protected $fillable = [
        'car_id','supplier_id','service_date','service_type','location','reported_issue',
        'activities_detail','pending_work','observations','inspector_name','officer_in_charge',
        'finalized','finalized_at','final_pdf_path','created_by_user_id','updated_by_user_id'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function documents()
    {
        return $this->hasMany(MaintenanceDocument::class);
    }
}
