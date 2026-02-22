<?php

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
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReportWhen(function (Throwable $e) {
            $botSpam = [
                'Filament\Notifications\Collection::fromLivewire',
                'Cannot assign array to property Filament\Notifications\Livewire\Notifications::$isFilamentNotificationsComponent',
                'An action tried to resolve without a name',
            ];

            foreach ($botSpam as $message) {
                if (str_contains($e->getMessage(), $message)) {
                    return true;
                }
            }

            return false;
        });
    })
    ->create();
