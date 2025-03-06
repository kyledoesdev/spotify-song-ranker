<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\EmailBlastNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendEmailBlast extends Command
{
    protected $signature = 'send-email-blast';
    protected $description = 'Send email blast to all users.';

    public function handle()
    {
        foreach (User::with('preferences')->get() as $user) {
            if ($user->preferences && $user->preferences->recieve_reminder_emails === true) {
                $this->info("Notifying: {$user->email}.");
                Notification::send($user, new EmailBlastNotification);
            }
        }
    }
}
