<?php

return [

    'channels' => [
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
