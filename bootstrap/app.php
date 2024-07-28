<?php

use App\Console\Commands\RankingReminderCommand;
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
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->throttleApi();
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(RankingReminderCommand::class)
            ->timezone('America/New_York')
            ->weeklyOn(1, '12:00')
            ->emailOutputOnFailure(env("FAILURE_EMAIL"));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
