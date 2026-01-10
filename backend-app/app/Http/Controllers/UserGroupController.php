<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\UserGroup;
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
class UserGroupController extends Controller
{
    public function getGroupsForUser(User $user): JsonResponse
    {
        return response()->json($user->groups);
    }

    public function getUsersForGroup(Group $group): JsonResponse
    {
        return response()->json($group->users);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{userId}/groups/{groupId}",
     *     tags={"Sistema"},
     *     summary="Asigna un usuario a un grupo",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="groupId",
     *         in="path",
     *         required=true,
     *         description="ID del grupo",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario asignado al grupo",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=10),
     *             @OA\Property(property="id_user", type="integer", example=1),
     *             @OA\Property(property="id_group", type="integer", example=2),
     *             @OA\Property(property="assigned_at", type="string", format="date", example="2025-12-05"),
     *             @OA\Property(property="removed_at", type="string", format="date", nullable=true, example=null),
     *             @OA\Property(property="id_user_created", type="integer", example=3),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Asignacion ya existe"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario o grupo no encontrado"
     *     )
     * )
     */
    public function assign(int $userId, int $groupId): JsonResponse
    {
        User::findOrFail($userId);
        Group::findOrFail($groupId);

        $existing = UserGroup::where('id_user', $userId)
            ->where('id_group', $groupId)
            ->whereNull('removed_at')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'El usuario ya esta asignado a este grupo'], 409);
        }

        $assignment = UserGroup::create([
            'id_user' => $userId,
            'id_group' => $groupId,
            'assigned_at' => Carbon::today()->toDateString(),
            'removed_at' => null,
            'id_user_created' => Auth::id(),
        ]);

        return response()->json($assignment, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{userId}/groups/{groupId}",
     *     tags={"Sistema"},
     *     summary="Desasigna un usuario de un grupo",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="groupId",
     *         in="path",
     *         required=true,
     *         description="ID del grupo",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Desasignacion registrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario desasignado del grupo"),
     *             @OA\Property(property="removed_at", type="string", format="date", example="2025-12-05")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No existe una asignacion activa para este usuario y grupo"
     *     )
     * )
     */
    public function remove(int $userId, int $groupId): JsonResponse
    {
        $assignment = UserGroup::where('id_user', $userId)
            ->where('id_group', $groupId)
            ->whereNull('removed_at')
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Asignacion no encontrada'], 404);
        }

        $assignment->removed_at = Carbon::today()->toDateString();
        $assignment->save();

        return response()->json([
            'message' => 'Usuario desasignado del grupo',
            'removed_at' => $assignment->removed_at,
        ]);
    }
}
