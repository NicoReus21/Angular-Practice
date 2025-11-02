<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = ['company_id','year','month','amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

