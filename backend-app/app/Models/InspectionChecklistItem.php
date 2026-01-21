<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_checklist_id',
        'inspection_category_id',
        'key',
        'label',
        'value',
        'comment',
    ];

    public function checklist()
    {
        return $this->belongsTo(InspectionChecklist::class, 'inspection_checklist_id');
    }

    public function category()
    {
        return $this->belongsTo(InspectionCategory::class, 'inspection_category_id');
    }
}
