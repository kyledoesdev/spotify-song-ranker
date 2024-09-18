<?php

use Illuminate\Support\Facades\Facade;

return [
    'aliases' => Facade::defaultAliases()->merge([
        'Bugsnag' => Bugsnag\BugsnagLaravel\Facades\Bugsnag::class,
    ])->toArray(),
];
