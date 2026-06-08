<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Controls which front-end origins may call this API from the browser.
    | Auth uses Bearer tokens (not cookies), so supports_credentials stays false.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'https://sabha-woad.vercel.app',
    ],

    // Match every Vercel preview/production deploy for this project
    // (e.g. https://sabha-8oa4gp3rm-sathwara72s-projects.vercel.app).
    'allowed_origins_patterns' => [
        '#^https://sabha-[a-z0-9-]+\.vercel\.app$#',
        '#^https://sabha-[a-z0-9-]+-sathwara72s-projects\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
