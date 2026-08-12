<?php

namespace App\Filament\Resources\Rankings\Filters;

use App\QueryBuilders\RankingQueryBuilder;
use Filament\Tables\Filters\Filter;

class InProcessFilter extends Filter
{
    public static function getDefaultName(): ?string
    {
        return 'in_process';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Hide In Process');

        $this->toggle();

        $this->default();

        $this->query(fn (RankingQueryBuilder $query): RankingQueryBuilder => $query->completed());
    }
}
