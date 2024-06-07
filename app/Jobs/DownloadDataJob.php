<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DownloadDataNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class DownloadDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Collection $rankings, private User $user){}

    public function handle(): void
    {
        Notification::send($this->user, new DownloadDataNotification($this->rankings));
    }
}
