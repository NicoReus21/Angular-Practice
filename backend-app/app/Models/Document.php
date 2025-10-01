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
     * @var array<int, string>
     */

    protected $fillable = [
        'process_id',
        'section_title',
        'step_title',
        'file_name',
        'file_path',
    ];
    // ------------------------------------

    /**
     * Get the process that owns the document.
     */
    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
