<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [


    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
        'Bugsnag' => Bugsnag\BugsnagLaravel\Facades\Bugsnag::class,
    ])->toArray(),

];
