<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="SiGBA API",
 *     version="1.0.0",
 *     description="Documentación de la API SiGBA"
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local"
 * )
 */
class OpenApiSpec
{
    // Archivo contenedor de las anotaciones raíz para Swagger.
}
