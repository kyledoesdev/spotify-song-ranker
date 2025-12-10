<?php

namespace App\Actions\Comments;

use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Comments\Actions\ResolveMentionsAutocompleteAction;

class CustomResolveMentionsAutocompleteAction extends ResolveMentionsAutocompleteAction
{
    public function resolve(string $query, $commentable): array
    {
        return User::query()
            ->where('name', 'like', "%$query%")
            ->whereHas('preferences', function (Builder $query) {
                $query->where('enabled_comment_mentions', true);
            })
            ->limit(10)
            ->get()
            ->toArray();
    }
}