<?php

namespace App\Http\Controllers;

use App\Models\UserRol;
use App\Http\Requests\StoreUserRolRequest;
use App\Http\Requests\UpdateUserRolRequest;
use App\Models\User; 
use App\Models\Rol; 

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
        return $user->rols; 
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
}