<?php

use Illuminate\Support\Facades\Log;
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

Schedule::command('artists:update-images')
    ->timezone('America/New_York')
    ->weeklyOn(5, '0:00') /* Friday at mightnight */
    ->onSuccess(function() {
        Log::channel('discord')->info('Artist Images Updated.');
    })
    ->onFailure(function() {
        Log::channel('discord')->info('Something went wrong updating artist images.');
    });
