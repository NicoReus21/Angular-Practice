<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CarChecklist;

class CarChecklistItems extends Model
{
    use HasFactory;

    protected $table = 'checklist_items';

    /**
     * The attributes that are mass assignable.
     *
     * 'checklist_id' se elimina de aquí porque es manejado
     * automáticamente por la relación.
     */
    protected $fillable = [
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