<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateFilters;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Spatie\Comments\Models\Comment;

class CommentsCreatedWidget extends ChartWidget
{
    use HasDateFilters;

    protected ?string $heading = 'Comment Stats';

    public ?string $filter = 'year';

    protected static ?int $sort = 6;
    
    protected function getData(): array
    {
        $trendConfig = $this->getTrendConfig($this->filter ?? 'year');

        $created = Trend::model(Comment::class)
            ->between(
                start: $trendConfig['start'],
                end: $trendConfig['end'],
            )
            ->{$trendConfig['period']}()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Comments Created',
                    'data' => $created->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#c084fc'
                ],
            ],
            'labels' => $trendConfig['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'day' => 'Past Day',
            'week' => 'Past Week',
            'month' => 'Past Month',
            'year' => 'Past Year',
        ];
    }
}