<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    /** @use HasFactory<\Database\Factories\UserGroupFactory> */
    use HasFactory;
    protected $fillable = [
        'id_user',
        'id_group',
        'assigned_at',
        'removed_at',
        'id_user_created',
    ];
}
