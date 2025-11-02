<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarDocument extends Model
{
    use HasFactory;

    protected $table = 'car_documents';
    protected $fillable = [
        'car_id','type','file_path','issue_date','expires_at','alert_sent_at','uploaded_by_user_id'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expires_at' => 'date',
        'alert_sent_at' => 'datetime',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}

