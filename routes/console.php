<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:clean')->timezone('America/New_York')->daily()->at('12:00');
Schedule::command('backup:run')
    ->timezone('America/New_York')
    ->daily()
    ->at('12:05')
    ->onFailure(function () {
        Log::channel('bugsnag')->error("BACK UP FAILED WEE WOO WEE WOO");
        Log::channel('discord')->error("BACK UP FAILED WEE WOO WEE WOO");
    })
    ->onSuccess(function () {
        Log::channel('bugsnag')->warning("BACK UP SUCCESS");
        Log::channel('discord')->warning("BACK UP SUCCESS");
    });