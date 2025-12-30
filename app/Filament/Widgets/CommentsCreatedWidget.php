<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateFilters;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Spatie\Comments\Models\Comment;

class CommentsCreatedWidget extends ChartWidget
{
    use HasDateFilters;
    use HasFiltersSchema;

    protected ?string $heading = 'Comment Stats';

    protected static ?int $sort = 6;

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components($this->getDateFiltersSchema());
    }

    protected function getData(): array
    {
        $trendConfig = $this->getTrendConfig();

        $created = Trend::model(Comment::class)
            ->between(start: $trendConfig['start'], end: $trendConfig['end'])
            ->{$trendConfig['period']}()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Comments Created',
                    'data' => $created->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#c084fc',
                ],
            ],
            'labels' => $trendConfig['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}