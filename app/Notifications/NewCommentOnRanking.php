<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Ranking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentOnRanking extends Notification
{
    use Queueable;

    public function __construct(
        public Comment $comment,
        public Ranking $ranking,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'entity' => [
                'id' => $this->ranking->getKey(),
                'name' => $this->ranking->name,
            ],
            'url' => route('ranking', ['id' => $this->ranking->getKey()])."#comment-{$this->comment->getKey()}",
            'message' => $this->comment->original_text,
            'user_name' => $this->comment->commentator?->name,
            'avatar' => $this->comment->commentator?->avatar,
            'comment_id' => $this->comment->getKey(),
        ];
    }
}
