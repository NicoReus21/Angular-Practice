<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Sistema",
 *     description="Autenticacion, usuarios, grupos, roles y permisos."
 * )
 * @OA\Tag(
 *     name="Material Mayor",
 *     description="Gestion de carros, mantenciones, checklists y documentos."
 * )
 * @OA\Tag(
 *     name="Bombero Accidentado",
 *     description="Procesos y carga de documentos del modulo Bombero Accidentado."
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum Token"
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="Group",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="id_parent_group", type="integer", nullable=true),
 *     @OA\Property(property="id_user_created", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="Rol",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="id_user_created", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="Permission",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="module", type="string"),
 *     @OA\Property(property="section", type="string"),
 *     @OA\Property(property="action", type="string", enum={"create","read","update","delete"}),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="Car",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="plate", type="string"),
 *     @OA\Property(property="model", type="string", nullable=true),
 *     @OA\Property(property="company", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", nullable=true),
 *     @OA\Property(property="imageUrl", type="string", nullable=true),
 *     @OA\Property(property="marca", type="string", nullable=true),
 *     @OA\Property(property="chassis_number", type="string", nullable=true),
 *     @OA\Property(property="type", type="string", nullable=true),
 *     @OA\Property(property="cabin", type="string", nullable=true),
 *     @OA\Property(property="mileage", type="integer", nullable=true),
 *     @OA\Property(property="hourmeter", type="integer", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="Maintenance",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="car_id", type="integer"),
 *     @OA\Property(property="service_date", type="string", format="date"),
 *     @OA\Property(property="mileage", type="integer", nullable=true),
 *     @OA\Property(property="hourmeter", type="integer", nullable=true),
 *     @OA\Property(property="service_type", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", nullable=true),
 *     @OA\Property(property="pdf_url", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="CarChecklist",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="car_id", type="integer"),
 *     @OA\Property(property="persona_cargo", type="string"),
 *     @OA\Property(property="fecha_realizacion", type="string", format="date"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="CarDocument",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="car_id", type="integer"),
 *     @OA\Property(property="cost", type="number", format="float", nullable=true),
 *     @OA\Property(property="file_name", type="string"),
 *     @OA\Property(property="path", type="string"),
 *     @OA\Property(property="file_type", type="string"),
 *     @OA\Property(property="is_paid", type="boolean"),
 *     @OA\Property(property="maintenance_id", type="integer", nullable=true),
 *     @OA\Property(property="url", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * @OA\Schema(
 *     schema="Process",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ApiDocs
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Sistema"},
     *     summary="Registrar usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password", minLength=6)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuario creado", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=422, description="Validacion")
     * )
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Sistema"},
     *     summary="Login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Token generado"),
     *     @OA\Response(response=401, description="Credenciales invalidas")
     * )
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Sistema"},
     *     summary="Logout",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="OK")
     * )
     * @OA\Get(
     *     path="/api/user",
     *     tags={"Sistema"},
     *     summary="Usuario autenticado",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Usuario", @OA\JsonContent(ref="#/components/schemas/User"))
     * )
     */
    public function authDocs()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Sistema"},
     *     summary="Listar usuarios",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")))
     * )
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Sistema"},
     *     summary="Crear usuario",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", minLength=6)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/User"))
     * )
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Sistema"},
     *     summary="Detalle usuario",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Usuario", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Sistema"},
     *     summary="Actualizar usuario",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", minLength=6)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/User"))
     * )
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Sistema"},
     *     summary="Eliminar usuario",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Eliminado")
     * )
     * @OA\Get(
     *     path="/api/users/{user}/roles",
     *     tags={"Sistema"},
     *     summary="Roles por usuario",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Roles", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Rol")))
     * )
     */
    public function userDocs()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/groups",
     *     tags={"Sistema"},
     *     summary="Listar grupos",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Group")))
     * )
     * @OA\Post(
     *     path="/api/groups",
     *     tags={"Sistema"},
     *     summary="Crear grupo",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="id_parent_group", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/Group"))
     * )
     * @OA\Get(
     *     path="/api/groups/{id}",
     *     tags={"Sistema"},
     *     summary="Detalle grupo",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Grupo", @OA\JsonContent(ref="#/components/schemas/Group")),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     * @OA\Put(
     *     path="/api/groups/{id}",
     *     tags={"Sistema"},
     *     summary="Actualizar grupo",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="id_parent_group", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Group"))
     * )
     * @OA\Delete(
     *     path="/api/groups/{id}",
     *     tags={"Sistema"},
     *     summary="Eliminar grupo",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Eliminado")
     * )
     */
    public function groupDocs()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/rols",
     *     tags={"Sistema"},
     *     summary="Listar roles",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Rol")))
     * )
     * @OA\Post(
     *     path="/api/rols",
     *     tags={"Sistema"},
     *     summary="Crear rol",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/Rol"))
     * )
     * @OA\Get(
     *     path="/api/rols/{id}",
     *     tags={"Sistema"},
     *     summary="Detalle rol",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Rol", @OA\JsonContent(ref="#/components/schemas/Rol"))
     * )
     * @OA\Put(
     *     path="/api/rols/{id}",
     *     tags={"Sistema"},
     *     summary="Actualizar rol",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Rol"))
     * )
     * @OA\Delete(
     *     path="/api/rols/{id}",
     *     tags={"Sistema"},
     *     summary="Eliminar rol",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Eliminado")
     * )
     * @OA\Get(
     *     path="/api/rols/{id}/permissions",
     *     tags={"Sistema"},
     *     summary="Permisos de un rol",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Permission")))
     * )
     * @OA\Post(
     *     path="/api/rols/{id}/permissions",
     *     tags={"Sistema"},
     *     summary="Sincronizar permisos de un rol",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"permissions"},
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permisos sincronizados", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Permission")))
     * )
     */
    public function rolDocs()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/permissions",
     *     tags={"Sistema"},
     *     summary="Listar permisos",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Permission")))
     * )
     * @OA\Get(
     *     path="/api/modules/bombero-accidentado/permissions",
     *     tags={"Sistema"},
     *     summary="Permisos Bombero Accidentado",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Permission")))
     * )
     * @OA\Get(
     *     path="/api/modules/material-mayor/permissions",
     *     tags={"Sistema"},
     *     summary="Permisos Material Mayor",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Permission")))
     * )
     */
    public function permissionDocs()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/cars",
     *     tags={"Material Mayor"},
     *     summary="Listar carros",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Car")))
     * )
     * @OA\Post(
     *     path="/api/cars",
     *     tags={"Material Mayor"},
     *     summary="Crear carro",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Car")),
     *     @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/Car"))
     * )
     * @OA\Get(
     *     path="/api/cars/{id}",
     *     tags={"Material Mayor"},
     *     summary="Detalle carro",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Carro", @OA\JsonContent(ref="#/components/schemas/Car"))
     * )
     * @OA\Put(
     *     path="/api/cars/{id}",
     *     tags={"Material Mayor"},
     *     summary="Actualizar carro",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Car")),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Car"))
     * )
     * @OA\Delete(
     *     path="/api/cars/{id}",
     *     tags={"Material Mayor"},
     *     summary="Eliminar carro",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Eliminado")
     * )
     */
    public function carDocs()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/cars/{car}/maintenances",
     *     tags={"Material Mayor"},
     *     summary="Crear mantenimiento",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="car", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Maintenance")),
     *     @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/Maintenance"))
     * )
     * @OA\Put(
     *     path="/api/maintenances/{maintenance}",
     *     tags={"Material Mayor"},
     *     summary="Actualizar mantenimiento",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="maintenance", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Maintenance")),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Maintenance"))
     * )
     * @OA\Delete(
     *     path="/api/maintenances/{maintenance}",
     *     tags={"Material Mayor"},
     *     summary="Eliminar mantenimiento",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="maintenance", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Eliminado")
     * )
     */
    public function maintenanceDocs()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/cars/{car}/checklists",
     *     tags={"Material Mayor"},
     *     summary="Crear checklist",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="car", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CarChecklist")),
     *     @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/CarChecklist"))
     * )
     * @OA\Put(
     *     path="/api/checklists/{checklist}",
     *     tags={"Material Mayor"},
     *     summary="Actualizar checklist",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CarChecklist")),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/CarChecklist"))
     * )
     * @OA\Delete(
     *     path="/api/checklists/{checklist}",
     *     tags={"Material Mayor"},
     *     summary="Eliminar checklist",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Eliminado")
     * )
     * @OA\Patch(
     *     path="/api/checklist-items/{item}/toggle",
     *     tags={"Material Mayor"},
     *     summary="Alternar item de checklist",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="item", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Actualizado")
     * )
     */
    public function checklistDocs()
    {
    }

    /**
     * @OA\Post(
     *     path="/api/cars/{car}/documents",
     *     tags={"Material Mayor"},
     *     summary="Subir documento de carro",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="car", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Multipart con archivo y metadata",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(property="file", type="string", format="binary"),
     *                 @OA\Property(property="cost", type="number", format="float"),
     *                 @OA\Property(property="maintenance_id", type="integer", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/CarDocument"))
     * )
     * @OA\Patch(
     *     path="/api/documents/{document}/toggle-payment",
     *     tags={"Material Mayor"},
     *     summary="Alternar pago de documento",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="document", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/CarDocument"))
     * )
     * @OA\Delete(
     *     path="/api/documents/{document}",
     *     tags={"Material Mayor"},
     *     summary="Eliminar documento",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="document", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Eliminado")
     * )
     */
    public function carDocumentDocs()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/process",
     *     tags={"Bombero Accidentado"},
     *     summary="Listar procesos",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Listado", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Process")))
     * )
     * @OA\Post(
     *     path="/api/process",
     *     tags={"Bombero Accidentado"},
     *     summary="Crear proceso",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *     @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/Process"))
     * )
     * @OA\Get(
     *     path="/api/process/{process}",
     *     tags={"Bombero Accidentado"},
     *     summary="Detalle proceso",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Proceso", @OA\JsonContent(ref="#/components/schemas/Process"))
     * )
     * @OA\Put(
     *     path="/api/process/{process}",
     *     tags={"Bombero Accidentado"},
     *     summary="Actualizar proceso",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Process"))
     * )
     * @OA\Delete(
     *     path="/api/process/{process}",
     *     tags={"Bombero Accidentado"},
     *     summary="Eliminar proceso",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Eliminado")
     * )
     * @OA\Patch(
     *     path="/api/processes/{process}/complete-step",
     *     tags={"Bombero Accidentado"},
     *     summary="Marcar paso de proceso como completado",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Process"))
     * )
     * @OA\Patch(
     *     path="/api/process/{process}/finalize",
     *     tags={"Bombero Accidentado"},
     *     summary="Finalizar proceso",
     *     description="Marca el proceso con estado Finalizado.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Proceso finalizado", @OA\JsonContent(ref="#/components/schemas/Process")),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function processDocs()
    {
    }

    /**
     * Endpoints de carga de archivos (parte 1).
     *
     * @OA\Post(
     *     path="/api/process/{process}/upload_reporte_flash",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir reporte flash",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_diab",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir DIAB",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_obac",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir OBAC",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_copia_libro_guardia",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir copia libro guardia",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_declaracion_testigo",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir declaracion testigo",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_certificado_carabineros",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir certificado carabineros",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_dau",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir DAU",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_informe_medico",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir informe medico",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_otros_documento_medico_adicional",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir documento medico adicional",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_certificado_medico_atencion_especial",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir certificado atencion especial",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_certificado_acreditacion_voluntario",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir certificado acreditacion voluntario",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_copia_libro_llamada",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir copia libro llamada",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_aviso_citacion",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir aviso citacion",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     */
    public function processUploadDocsA()
    {
    }

    /**
     * Endpoints de carga de archivos (parte 2).
     *
     * @OA\Post(
     *     path="/api/process/{process}/upload_copia_lista_asistencia",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir copia lista asistencia",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_informe_ejecutivo",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir informe ejecutivo",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_factura_prestaciones",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir factura prestaciones",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_boleta_honorarios_visada",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir boleta honorarios visada",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_boleta_medicamentos",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir boleta medicamentos",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_certificado_medico_autorizacion_examen",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir certificado autorizacion examen",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_boleta_factura_traslado",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir boleta factura traslado",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_certificado_medico_traslado",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir certificado medico traslado",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_boleta_gastos_acompanante",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir boleta gastos acompanante",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_boleta_alimentacion_acompanante",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir boleta alimentacion acompanante",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_certificado_medico_incapacidad",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir certificado medico incapacidad",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     * @OA\Post(
     *     path="/api/process/{process}/upload_otros_gastos",
     *     tags={"Bombero Accidentado"},
     *     summary="Subir otros gastos",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="process", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(required={"file"}, @OA\Property(property="file", type="string", format="binary")))),
     *     @OA\Response(response=201, description="Archivo guardado")
     * )
     */
    public function processUploadDocsB()
    {
    }

}
