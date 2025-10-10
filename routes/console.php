<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;

Schedule::command('rankings:reminder')
    ->timezone('America/New_York')
    ->weeklyOn(5, '12:00') /* Friday at noon. */
    ->onSuccess(function () {
        Log::channel('discord_other_updates')->info('Reminded users to complete rankings successfully.');
    })
    ->onFailure(function () {
        Log::channel('discord_other_updates')->info('Something went wrong sending ranking reminder emails.');
    });

Schedule::command('artists:update-images')
    ->timezone('America/New_York')
    ->dailyAt('08:00') /* daily at 8am */
    ->onSuccess(function () {
        Log::channel('discord_other_updates')->info('Artist Images updated successfully.');
    })
    ->onFailure(function () {
        Log::channel('discord_other_updates')->info('Something went wrong updating artist images.');
    });

Schedule::command('newsletter:send')
    ->timezone('America/New_York')
    ->monthlyOn(15, '3:00')
    ->onSuccess(function () {
        Log::channel('discord_other_updates')->info('Newsletter sent successfully.');
    })
    ->onFailure(function () {
        Log::channel('discord_other_updates')->info('Something went wrong sending news letter emails.');
    });

Schedule::command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
Schedule::command('model:prune', [
    '--model' => [
        \Spatie\Health\Models\HealthCheckResultHistoryItem::class,
    ],
])->daily();
