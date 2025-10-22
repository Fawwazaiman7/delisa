<?php

$envOrigins = array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', ''))));

if (empty($envOrigins)) {
    $envOrigins[] = env('APP_URL');
}

if (env('APP_ENV') === 'local') {
    $envOrigins[] = 'http://localhost:5173';
    $envOrigins[] = 'http://127.0.0.1:5173';
}

$origins = array_values(array_unique(array_filter($envOrigins)));

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => $origins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Accept', 'Authorization', 'Content-Type', 'X-CSRF-TOKEN', 'X-Requested-With', 'Origin'],

    'exposed_headers' => ['Link'],

    'max_age' => 3600,

    'supports_credentials' => true,
];
