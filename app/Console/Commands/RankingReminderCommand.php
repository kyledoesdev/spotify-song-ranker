<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\RankingReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class RankingReminderCommand extends Command
{
    protected $signature = 'rankings:reminder';

    protected $description = 'Remind users that they have uncompleted ranking(s).';

    public function handle()
    {   
        $users = User::query()
            ->forRankingReminders()
            ->get();

        Log::channel('discord')->info("Reminding {$users->count()} to complete their rankings.");
            
        $users->each(function(User $user) {
            if ($user->preferences && $user->preferences->recieve_reminder_emails === true) {
                Notification::send($user, new RankingReminderNotification($user->rankings));

                Log::channel('single')->info("Notifying: {$user->email}");

                sleep(2);
            }
        });      
    }
}
