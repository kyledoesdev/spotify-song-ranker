<?php

namespace App\Actions\Comments;

use Blaspsoft\Blasp\Facades\Blasp;
use Spatie\Comments\Models\Comment;
use Spatie\Comments\CommentTransformers\CommentTransformer;

final class FilterCommentProfanityAction implements CommentTransformer
{
    /**
     * Execute the action.
     */
    public function handle(Comment $comment): void
    {
        $comment->text = Blasp::allLanguages()
            ->maskWith('*')
            ->check($comment->original_text)
            ->getCleanString();
    }
}