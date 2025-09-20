<?php

namespace App\Console\Commands;

use App\Models\Ranking;
use App\Models\User;
use App\Notifications\Newsletter;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendNewsletterEmails extends Command
{
    protected $signature = 'newsletter:send';

    protected $description = 'Sends the Newsletter emails';

    public function handle()
    {
        $rankings = Ranking::query()
            ->forNewsletter()
            ->get();

        if (count($rankings) <= 0) {
            return Command::SUCCESS;
        }

        $users = User::query()
            ->whereHas('preferences', fn (Builder $query): Builder => $query->where('recieve_newsletter_emails', true))
            ->get();

        Log::channel('discord_other_updates')->info("Sending {$users->count()} users the monthly newsletter of {$rankings->count()}.");

        Notification::send($users, new Newsletter($rankings));

        Log::channel('discord_other_updates')->info('Completed Newsletter send.');

        return Command::SUCCESS;
    }
}
