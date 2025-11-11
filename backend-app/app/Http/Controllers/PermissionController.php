<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * Devuelve la lista de TODOS los permisos
     */
    public function index()
    {
        return Permission::all();
    }

    /**
     * Crea un nuevo permiso.
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->validated());
        return response()->json($permission, 201);
    }

    /**
     * Muestra un permiso específico.
     */
    public function show(Permission $permission)
    {
        return $permission;
    }

    /**
     * Actualiza un permiso.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());
        return response()->json($permission);
    }

    /**
     * Elimina un permiso.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null, 204);
    }

}
