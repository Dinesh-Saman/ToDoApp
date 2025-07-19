<?php

return [
    'paths' => ['api/*'], // Removed 'sanctum/csrf-cookie'
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173'], // Your frontend URL
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false, // Changed to false since we're not using authentication
];