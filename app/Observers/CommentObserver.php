<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Ranking;
use App\Notifications\NewCommentOnRanking;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $commentable = $comment->commentable;

        if (! $commentable instanceof Ranking) {
            return;
        }

        $rankingOwner = $commentable->user;

        if (! $rankingOwner || $rankingOwner->getKey() === $comment->commentator_id) {
            return;
        }

        $rankingOwner->notify(new NewCommentOnRanking($comment, $commentable));
    }

    public function deleted(Comment $comment): void
    {
        Notification::query()
            ->whereNull('read_at')
            ->where('type', NewCommentOnRanking::class)
            ->whereJsonContains('data->comment_id', $comment->getKey())
            ->delete();
    }
}
