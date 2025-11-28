<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MaintenanceDocument extends Model
{
    use HasFactory;

    protected $table = 'maintenance_documents';
    
    protected $fillable = [
        'maintenance_id',
        'file_path',
        'mime',
        'size',
        'uploaded_by_user_id'
    ];

    // IMPORTANTE: Esto envía el campo 'url' en el JSON hacia Angular
    protected $appends = ['url'];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    /**
     * Genera la URL pública para ver la imagen en el navegador.
     */
    public function getUrlAttribute()
    {
        if (!$this->file_path) return null;
        return Storage::url($this->file_path);
    }
}