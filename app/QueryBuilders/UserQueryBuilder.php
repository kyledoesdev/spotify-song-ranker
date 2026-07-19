<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class UserQueryBuilder extends Builder
{
    public function topCreators(int $limit = 10): static
    {
        return $this->newQuery()
            ->whereHas('rankings', fn (Builder $query) => $query->public()->completed())
            ->withCount(['rankings' => fn (Builder $query) => $query->public()->completed()])
            ->orderByDesc('rankings_count')
            ->orderBy('name')
            ->limit($limit);
    }

    public function roundedUserCount(): int
    {
        return (int) (round($this->newQuery()->count() / 50) * 50);
    }
}
