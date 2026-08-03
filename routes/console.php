<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
use Spatie\Health\Models\HealthCheckResultHistoryItem;

Schedule::command('artists:update-images')
    ->timezone('America/New_York')
    ->dailyAt('08:00') /* daily at 8am */
    ->onFailure(function () {
        Log::channel('discord_other_updates')->info('Something went wrong updating artist images. You may need to refresh SOP token.');
    });

Schedule::command('daily-digest:send')
    ->timezone('America/New_York')
    ->dailyAt('00:00') /* daily at 12an */
    ->onFailure(function () {
        Log::channel('discord_other_updates')->info('Something went wrong sending daily digest update.');
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
        HealthCheckResultHistoryItem::class,
    ],
])->daily();
