<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Group extends Model
{
    /** @use HasFactory<\Database\Factories\GroupFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'id_parent_group',
        'id_user_created',
    ];
    public function user_groups()
    {
        return $this->hasMany(UserGroup::class, 'id_group', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_groups', 'id_group', 'id_user')
            ->withPivot(['assigned_at', 'removed_at'])
            ->wherePivotNull('removed_at');
    }
}
