<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Group;
use App\Models\Rol;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

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

    public function hasPermission(string $module, string $section, string $action): bool
    {
        $permissionId = Permission::where('module', $module)
            ->where('section', $section)
            ->where('action', $action)
            ->value('id');

        if (!$permissionId) {
            return false;
        }

        $userId = $this->id;

        $direct = DB::table('user_permissions')
            ->where('id_user', $userId)
            ->where('id_permission', $permissionId)
            ->whereNull('revoked_at')
            ->exists();

        if ($direct) {
            return true;
        }

        $fromGroups = DB::table('user_groups')
            ->join('group_permissions', 'group_permissions.id_group', '=', 'user_groups.id_group')
            ->where('user_groups.id_user', $userId)
            ->whereNull('user_groups.removed_at')
            ->where('group_permissions.id_permission', $permissionId)
            ->whereNull('group_permissions.revoked_at')
            ->exists();

        if ($fromGroups) {
            return true;
        }

        return DB::table('user_rols')
            ->join('rol_permission', 'rol_permission.rol_id', '=', 'user_rols.id_rol')
            ->where('user_rols.id_user', $userId)
            ->whereNull('user_rols.removed_at')
            ->where('rol_permission.permission_id', $permissionId)
            ->exists();
    }
}
