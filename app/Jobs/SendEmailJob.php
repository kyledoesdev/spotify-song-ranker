<?php

namespace App\Jobs;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Notifications\EmailBlastNotification;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private EmailTemplate $emailTemplate) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::query()
            ->whereHas('preferences', fn (Builder $query): Builder => $query->where('recieve_reminder_emails', true))
            ->get();

        foreach ($users as $user) {
            Notification::send($user, new EmailBlastNotification($this->emailTemplate));
        }

        Log::channel('discord')->info("Blasted {$users->count()} users.");
    }
}
