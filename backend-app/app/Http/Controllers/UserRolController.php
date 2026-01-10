<?php

namespace App\Http\Controllers;

use App\Models\UserRol;
use App\Http\Requests\StoreUserRolRequest;
use App\Http\Requests\UpdateUserRolRequest;
use App\Models\User;
use App\Models\Rol;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserRolController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(UserRol::class, 'user_rol');
    }
    /**
     * Obtiene los roles asignados a un usuario específico.
     */
    public function getRolesForUser(User $user)
    {
        $this->authorize('viewAny', UserRol::class);
        return $user->rols()->with('permissions')->get();
    }

    /**
     * Asigna un rol a un usuario.
     */
    public function store(StoreUserRolRequest $request)
    {
        $validated = $request->validated();

        $exists = UserRol::where('user_id', $validated['user_id'])
                        ->where('rol_id', $validated['rol_id'])
                        ->exists();

        if ($exists) {
            return response()->json(['message' => 'El usuario ya tiene este rol.'], 422);
        }

        $userRol = UserRol::create($validated);
        return response()->json($userRol, 201);
    }


    /**
     * Quita un rol a un usuario.
     */
    public function destroy(UserRol $userRol)
    {
        $this->authorize('delete', $userRol);
        $userRol->delete();
        return response()->json(null, 204);
    }

    public function assign(int $userId, int $roleId): JsonResponse
    {
        User::findOrFail($userId);
        Rol::findOrFail($roleId);

        $existing = UserRol::where('id_user', $userId)
            ->where('id_rol', $roleId)
            ->whereNull('removed_at')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'El usuario ya tiene este rol.'], 409);
        }

        $assignment = UserRol::create([
            'id_user' => $userId,
            'id_rol' => $roleId,
            'assigned_at' => Carbon::today()->toDateString(),
            'removed_at' => null,
            'id_user_created' => Auth::id(),
        ]);

        return response()->json($assignment, 201);
    }

    public function remove(int $userId, int $roleId): JsonResponse
    {
        $assignment = UserRol::where('id_user', $userId)
            ->where('id_rol', $roleId)
            ->whereNull('removed_at')
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Asignacion no encontrada'], 404);
        }

        $assignment->removed_at = Carbon::today()->toDateString();
        $assignment->save();

        return response()->json([
            'message' => 'Rol desasignado del usuario',
            'removed_at' => $assignment->removed_at,
        ]);
    }
}
