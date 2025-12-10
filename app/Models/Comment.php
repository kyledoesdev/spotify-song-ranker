<?php

namespace App\Models;

use App\Concerns\StatsAfterEvents;
use Spatie\Comments\Models\Comment as SpatieComment;

class Comment extends SpatieComment
{
    use StatsAfterEvents;
}