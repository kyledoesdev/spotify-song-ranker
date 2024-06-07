<?php

namespace App\Notifications;

use App\Exports\RankingsExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class DownloadDataNotification extends Notification
{
    use Queueable;

    public function __construct(private Collection $rankings){}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->markdown('emails.downloaded-data', ['notifiable' => $notifiable])
            ->from('no-reply@kyles-song-ranker.com')
            ->subject("Kyle's Song Ranker - Data Download Complete")
            ->attach(Excel::download(new RankingsExport($this->rankings), 'rankings.xlsx')->getFile(), ['as' => 'rankings.xlsx']);
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
