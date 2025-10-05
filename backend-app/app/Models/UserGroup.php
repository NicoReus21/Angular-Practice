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

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'id_group', 'id');
    }
}
