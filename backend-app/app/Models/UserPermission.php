<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    /** @use HasFactory<\Database\Factories\UserPermissionFactory> */
    use HasFactory;
    protected $fillable = [
        'id_user',
        'id_permission',
        'granted_at',
        'revoked_at',
        'id_user_created',
    ];
}
