<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CarChecklist;

class CarChecklistItem extends Model
{
    use HasFactory;

    protected $table = 'checklist_items';

    protected $fillable = [
        'checklist_id',
        'task_description',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    /**
     * Una tarea (item) pertenece a UN checklist.
     */
    public function checklist()
    {
        return $this->belongsTo(CarChecklist::class, 'checklist_id');
    }
}