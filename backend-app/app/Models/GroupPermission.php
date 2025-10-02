<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPermission extends Model
{
    /** @use HasFactory<\Database\Factories\GroupPermissionFactory> */
    use HasFactory;
    protected $fillable = [
        'id_group',
        'id_permission',
        'granted_at',
        'revoked_at',
        'id_user_created',
    ];
}
