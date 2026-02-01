<?php

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => env('APP_NAME', 'ProjectAtlas API'),
            ],

            'routes' => [
                'api' => '/swagger/',
                'docs' => '/swagger/docs',
                'oauth2_callback' => '/swagger/oauth2-callback',
            ],

            'paths' => [
                'docs' => storage_path('api-docs'),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => env('L5_SWAGGER_FORMAT', 'json'),
                'annotations' => [
                    base_path('app/Http/Controllers'),
                    base_path('app/Http/Resources'),
                ],
            ],
        ],
    ],

    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
    'generate_yaml_copy' => true,
    'proxy' => false,
    'additional_config_url' => null,
    'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),
    'validator_url' => env('L5_SWAGGER_VALIDATOR_URL', null),

    'constants' => [],
];
