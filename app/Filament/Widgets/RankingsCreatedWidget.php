<?php

namespace App\Filament\Widgets;

use App\Models\Ranking;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RankingsCreatedWidget extends ChartWidget
{
    protected ?string $heading = 'Rankings Stats';

    protected function getData(): array
    {
        $created = Trend::model(Ranking::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $deleted = Trend::query(Ranking::onlyTrashed())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $completed = Trend::query(Ranking::query()->whereNotNull('completed_at'))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Rankings Created',
                    'data' => $created->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#86efac'
                ],
                [
                    'label' => 'Rankings Deleted',
                    'data' => $deleted->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f87171'
                ],
                [
                    'label' => 'Rankings Completed',
                    'data' => $completed->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#c084fc'
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
