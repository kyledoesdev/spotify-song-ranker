<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class Newsletter extends Notification
{
    use Queueable;

    public function __construct(private Collection $rankings) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $html = "";

        $rankings = $this->rankings->shuffle()->take(25);
        
        foreach ($rankings as $ranking) {
            $html .= view('components.emails.ranking-card', ['ranking' => $ranking])->render();
        }

        return (new MailMessage)->view('emails.newsletter', [
            'notifiable' => $notifiable,
            'html' => $html,
            'rankingsCount' => $this->rankings->count()
        ])->subject("Kyle from songrank.dev - Monthly Newsletter");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
