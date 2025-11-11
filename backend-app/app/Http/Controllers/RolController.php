<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Http\Requests\StoreRolRequest;
use App\Http\Requests\UpdateRolRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Rol::class, 'rol');
    }

    public function index()
    {
        return Rol::all();
    }

    public function store(StoreRolRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['id_user_created'] = Auth::id();
        $rol = Rol::create($validatedData);
        return response()->json($rol, 201);
    }

    public function show(Rol $rol)
    {
        return $rol;
    }

    public function update(UpdateRolRequest $request, Rol $rol)
    {
        $rol->update($request->validated());
        return response()->json($rol);
    }

    public function destroy(Rol $rol)
    {
        $rol->delete();
        return response()->json(null, 204);
    }


    public function getPermissions(Rol $rol)
    {
        $this->authorize('view', $rol);
        return $rol->permissions()->get();
    }

    public function syncPermissions(Request $request, Rol $rol)
    {
        $this->authorize('update', $rol);

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $rol->permissions()->sync($request->permissions);

        return response()->json($rol->permissions, 200);
    }
}