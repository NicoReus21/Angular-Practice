<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Group;
use App\Models\Rol;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function user_groups()
    {
        return $this->hasMany(UserGroup::class, 'id_user', 'id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_groups', 'id_user', 'id_group')
            ->withPivot(['assigned_at', 'removed_at'])
            ->wherePivotNull('removed_at');
    }

    public function rols()
    {
        return $this->belongsToMany(Rol::class, 'user_rols', 'id_user', 'id_rol')
            ->withPivot(['assigned_at', 'removed_at'])
            ->wherePivotNull('removed_at');
    }
}
