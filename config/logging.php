<?php

return [
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'stderr'],
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],

        'vapor' => [
            'driver' => 'stack',
            // Add bugsnag to the stack:
            'channels' => ['bugsnag', 'stderr'],
        ],

        'bugsnag' => [
            'driver' => 'bugsnag',
        ],
    ],
];
