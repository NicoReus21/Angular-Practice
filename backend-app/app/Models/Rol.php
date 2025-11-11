<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;
use App\Models\User;

class Rol extends Model
{
    /** @use HasFactory<\Database\Factories\RolFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'id_user_created',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'rol_permission');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_rols');
    }
}
