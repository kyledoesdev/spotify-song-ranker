<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class RankingReminderNotification extends Notification
{
    use Queueable;

    public function __construct(private Collection $rankings){}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->markdown('emails.ranking_reminder', [
            'notifiable' => $notifiable,
            'rankings' => $this->rankings
        ])->subject("You have incomplete song-rank.com rankings.");
    }
}
