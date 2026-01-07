<?php

namespace App\Models;

use Kyledoesdev\Essentials\Concerns\HasStatsAfterEvents;
use Spatie\Comments\Models\Comment as SpatieComment;

class Comment extends SpatieComment
{
    use HasStatsAfterEvents;
}