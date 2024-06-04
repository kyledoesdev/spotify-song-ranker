<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;

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
