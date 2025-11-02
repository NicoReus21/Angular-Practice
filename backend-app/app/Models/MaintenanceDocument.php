<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceDocument extends Model
{
    use HasFactory;

    protected $table = 'maintenance_documents';
    protected $fillable = [
        'maintenance_id','file_path','mime','size','uploaded_by_user_id'
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}

