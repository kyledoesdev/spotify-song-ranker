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
        foreach(User::all() as $user) {
            if ($user->email && $user->email != "") {
                $this->info("Notifying: {$user->email}.");
                Notification::send($user, new EmailBlastNotification);
            }
        }
    }
}
