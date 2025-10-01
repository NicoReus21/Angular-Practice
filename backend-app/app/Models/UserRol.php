<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRol extends Model
{
    /** @use HasFactory<\Database\Factories\UserRolFactory> */
    use HasFactory;
    protected $fillable = [
        'id_user',
        'id_rol',
        'assigned_at',
        'removed_at',
        'id_user_created',
    ];
}
