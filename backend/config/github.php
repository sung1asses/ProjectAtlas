<?php

return [
    'token' => env('GITHUB_TOKEN'),
    'base_url' => env('GITHUB_API_BASE', 'https://api.github.com'),
    'cache_ttl' => env('GITHUB_CACHE_TTL', 300),
];
