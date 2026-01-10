<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($user, 201);
    }

    public function show(string $id)
    {
        return User::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }

    public function permissions(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = $user->id;

        $directPermissionIds = DB::table('user_permissions')
            ->where('id_user', $userId)
            ->whereNull('revoked_at')
            ->pluck('id_permission');

        $groupIds = DB::table('user_groups')
            ->where('id_user', $userId)
            ->whereNull('removed_at')
            ->pluck('id_group');

        $groupPermissionIds = DB::table('group_permissions')
            ->whereIn('id_group', $groupIds)
            ->whereNull('revoked_at')
            ->pluck('id_permission');

        $roleIds = DB::table('user_rols')
            ->where('id_user', $userId)
            ->whereNull('removed_at')
            ->pluck('id_rol');

        $rolePermissionIds = DB::table('rol_permission')
            ->whereIn('rol_id', $roleIds)
            ->pluck('permission_id');

        $permissionIds = $directPermissionIds
            ->merge($groupPermissionIds)
            ->merge($rolePermissionIds)
            ->unique()
            ->values();

        if ($permissionIds->isEmpty()) {
            return response()->json([]);
        }

        return Permission::whereIn('id', $permissionIds)
            ->orderBy('module')
            ->orderBy('section')
            ->orderBy('action')
            ->get();
    }
}
