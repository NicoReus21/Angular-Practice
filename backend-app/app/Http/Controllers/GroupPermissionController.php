<?php

namespace App\Http\Controllers;

use App\Models\GroupPermission;
use App\Models\Group;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Sistema",
 *     description="Operaciones del sistema (usuarios, grupos, roles)"
 * )
 */
class GroupPermissionController extends Controller
{
    public function getPermissionsForGroup(Group $group): JsonResponse
    {
        return response()->json($group->permissions);
    }

    /**
     * @OA\Post(
     *     path="/api/groups/{groupId}/permissions/{permissionId}",
     *     tags={"Sistema"},
     *     summary="Asigna un permiso a un grupo",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="groupId",
     *         in="path",
     *         required=true,
     *         description="ID del grupo",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="permissionId",
     *         in="path",
     *         required=true,
     *         description="ID del permiso",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Permiso asignado al grupo",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=12),
     *             @OA\Property(property="id_group", type="integer", example=2),
     *             @OA\Property(property="id_permission", type="integer", example=5),
     *             @OA\Property(property="granted_at", type="string", format="date", example="2025-12-05"),
     *             @OA\Property(property="revoked_at", type="string", format="date", nullable=true, example=null),
     *             @OA\Property(property="id_user_created", type="integer", example=3),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="El permiso ya esta asignado al grupo"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grupo o permiso no encontrado"
     *     )
     * )
     */
    public function assign(int $groupId, int $permissionId): JsonResponse
    {
        Group::findOrFail($groupId);
        Permission::findOrFail($permissionId);

        $existing = GroupPermission::where('id_group', $groupId)
            ->where('id_permission', $permissionId)
            ->whereNull('revoked_at')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'El permiso ya esta asignado al grupo'], 409);
        }

        $assignment = GroupPermission::create([
            'id_group' => $groupId,
            'id_permission' => $permissionId,
            'granted_at' => Carbon::today()->toDateString(),
            'revoked_at' => null,
            'id_user_created' => Auth::id(),
        ]);

        return response()->json($assignment, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/groups/{groupId}/permissions/{permissionId}",
     *     tags={"Sistema"},
     *     summary="Revoca un permiso de un grupo",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="groupId",
     *         in="path",
     *         required=true,
     *         description="ID del grupo",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="permissionId",
     *         in="path",
     *         required=true,
     *         description="ID del permiso",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permiso revocado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permiso revocado del grupo"),
     *             @OA\Property(property="revoked_at", type="string", format="date", example="2025-12-05")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No existe una asignacion activa para este grupo y permiso"
     *     )
     * )
     */
    public function revoke(int $groupId, int $permissionId): JsonResponse
    {
        $assignment = GroupPermission::where('id_group', $groupId)
            ->where('id_permission', $permissionId)
            ->whereNull('revoked_at')
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Asignacion no encontrada'], 404);
        }

        $assignment->revoked_at = Carbon::today()->toDateString();
        $assignment->save();

        return response()->json([
            'message' => 'Permiso revocado del grupo',
            'revoked_at' => $assignment->revoked_at,
        ]);
    }
}
