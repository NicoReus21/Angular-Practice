<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'process_id',
        'user_id',
        'file_name',
        'file_path',
        'section_title',
        'step',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = ['step_title'];

    /**
     * Get the step_title attribute.
     *
     * Este accesor crea una propiedad 'step_title' virtual en la respuesta JSON,
     * que es lo que el frontend necesita para funcionar correctamente.
     *
     * @return string
     */
    public function getStepTitleAttribute()
    {
        return $this->section_title;
    }

    /**
     * Get the process that owns the document.
     */
    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}

