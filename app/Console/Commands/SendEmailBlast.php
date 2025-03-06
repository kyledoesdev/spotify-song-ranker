<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\EmailBlastNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendEmailBlast extends Command
{
    protected $signature = 'send-email-blast';
    protected $description = 'Send email blast to all users.';

    public function handle()
    {
        $users = User::query()
            ->with('preferences', fn($query) => $query->where('recieve_reminder_emails', true))
            ->get();

        foreach ($users as $user) {
            if ($user->preferences->recieve_reminder_emails == true) {
                Log::info("Notifying: {$user->email}.");
                Notification::send($user, new EmailBlastNotification);
            } else {
                Log::info("SKIPPING: {$user->email}.");
            }            
        }

        Log::channel('discord')->info("Blasted {$user->count()} users.");
    }
}
