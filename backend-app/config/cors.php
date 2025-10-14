<?php

return [
    // La clave 'paths' debe incluir la ruta de la cookie CSRF de Sanctum.
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    
    'allowed_methods' => ['*'],
    
    // Estos orígenes están correctos.
    'allowed_origins' => ['http://localhost:4200'],
    
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    
    // Esto está correcto y es esencial.
    'supports_credentials' => true,
];
