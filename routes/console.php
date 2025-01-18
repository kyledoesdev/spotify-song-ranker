<?php

use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('rankings:reminder')
    ->timezone('America/New_York')
    ->weeklyOn(5, '12:00') /* Friday at noon. */
    ->onSuccess(function() {
        Log::channel('discord')->info('Reminded users to complete rankings successfully.');
    })
    ->onFailure(function() {
        Log::channel('discord')->info('Something went wrong sending ranking reminder emails.');
    });