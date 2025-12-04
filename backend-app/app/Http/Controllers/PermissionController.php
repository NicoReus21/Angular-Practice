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
     * Devuelve todos los permisos asociados a un mĆódulo especĆ­fico.
     * Actualmente se exponen slugs para los mĆódulos esperados y se validan
     * para evitar consultas arbitrarias.
     */
    public function byModule(string $module)
    {
        $moduleMap = [
            'bombero-accidentado' => 'Bombero Accidentado',
            'material-mayor'      => 'Material Mayor',
        ];

        if (!array_key_exists($module, $moduleMap)) {
            return response()->json(['message' => 'MĆódulo no encontrado'], 404);
        }

        return Permission::where('module', $moduleMap[$module])
            ->orderBy('section')
            ->orderBy('action')
            ->get();
    }

    /**
     * Alias dedicado para permisos de Bombero Accidentado.
     */
    public function bomberoAccidentado()
    {
        return $this->byModule('bombero-accidentado');
    }

    /**
     * Alias dedicado para permisos de Material Mayor.
     */
    public function materialMayor()
    {
        return $this->byModule('material-mayor');
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
