<?php

namespace App\Jobs;

use App\Models\User;
use Spatie\Comments\Models\Comment;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateUsernameInComments implements ShouldQueue
{
    use Queueable;

    public function __construct(private User $user) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Comment::query()
            ->whereLike("original_text", "%data-mention=\"{$this->user->getKey()}\"%")
            ->get()
            ->each(fn (Comment $comment) => $comment->save());
    }
}
