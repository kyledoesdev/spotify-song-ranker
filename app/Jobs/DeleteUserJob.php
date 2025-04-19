<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DownloadDataNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class DeleteUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private User $user) {}

    public function handle(): void
    {
        /* Get the user's rankings. */
        $rankings = $this->user
            ->rankings()
            ->with('songs', 'artist')
            ->get();

        /* Send the user their data. */
        if (count($rankings)) {
            Notification::send($this->user, new DownloadDataNotification($rankings));

            /* Delete the user's rankings & their songs in 1 transaction to be safe */
            DB::transaction(function () use ($rankings) {
                $rankings->each(function ($ranking) {
                    $ranking->songs()->delete();
                    $ranking->delete();
                });
            });
        }

        $this->user->delete();
    }
}
