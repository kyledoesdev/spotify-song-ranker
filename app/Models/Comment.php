<?php

namespace App\Models;

use App\Observers\CommentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Kyledoesdev\Essentials\Concerns\HasStatsAfterEvents;
use Spatie\Comments\Models\Comment as SpatieComment;

#[ObservedBy(CommentObserver::class)]
class Comment extends SpatieComment
{
    use HasStatsAfterEvents;
}
