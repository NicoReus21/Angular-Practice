<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Car;

class CarDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'cost',
        'file_name', 
        'path',      
        'file_type',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    protected $appends = ['url'];

    /**
     * Un documento pertenece a un Carro.
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}