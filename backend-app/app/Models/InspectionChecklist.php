<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'inspected_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'inspected_at' => 'date',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function items()
    {
        return $this->hasMany(InspectionChecklistItem::class, 'inspection_checklist_id');
    }
}
