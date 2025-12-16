<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    /** @use HasFactory<\Database\Factories\ProcessFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'bombero_name',
        'company',
        'status',
        'user_id',

    ];
    // ------------------------------------
    //protected $casts = [
    //    'sections_data' => 'array', 
    //];
    /**
     * Get the documents for the process.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Normaliza el estado al devolverlo en respuestas.
     */
    public function getStatusAttribute($value)
    {
        if ($value === 'started' || $value === 'Pendiente') {
            return 'Iniciado';
        }
        return $value;
    }
}
