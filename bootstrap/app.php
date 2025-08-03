<?php

use App\Providers\AppServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \Laravel\Socialite\SocialiteServiceProvider::class,
        \SocialiteProviders\Manager\ServiceProvider::class,
        \Bugsnag\BugsnagLaravel\BugsnagServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('welcome'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));

        $middleware->throttleApi();

        $middleware->validateCsrfTokens(except: [
            'support-bubble',
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {})
    ->withExceptions(function (Exceptions $exceptions) {})
    ->create();
