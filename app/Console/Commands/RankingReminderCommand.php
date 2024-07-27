<?php

namespace App\Console\Commands;

use App\Models\Ranking;
use App\Notifications\RankingReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class RankingReminderCommand extends Command
{
    protected $signature = 'rankings:unfinished-ranking-reminder';

    protected $description = 'Remind users that they have an unfinished ranking.';

    public function handle()
    {
        Ranking::query()
            ->whereNull('is_ranked')
            ->with('user', 'rankings')
            ->with('user.preferences', fn($q) => $q->where('recieve_reminder_emails', true))
            ->get()
            ->each(function($user) {
                Notification::send($user, new RankingReminderNotification($user->rankings));
            });
    }
}
