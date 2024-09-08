<?php

namespace App\Console\Commands;

use App\Jobs\ScheduledJobIsRunning;
use App\Models\User;
use App\Notifications\RankingReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class RankingReminderCommand extends Command
{
    protected $signature = 'rankings:reminder';

    protected $description = 'Remind users that they have an unfinished ranking.';

    public function handle()
    {   
        dispatch(new ScheduledJobIsRunning("Ranking Reminder Job"));

        User::query()
            ->forRankingReminders()
            ->get()
            ->each(function($user) {
                if ($user->preferences && $user->preferences->recieve_reminder_emails === true) {
                    Notification::send($user, new RankingReminderNotification($user->rankings));
                }
            });          
    }
}
