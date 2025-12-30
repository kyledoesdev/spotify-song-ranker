<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateFilters;
use App\Models\Ranking;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RankingsCreatedWidget extends ChartWidget
{
    use HasDateFilters;
    use HasFiltersSchema;

    protected ?string $heading = 'Rankings Stats';

    protected static ?int $sort = 3;

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components($this->getDateFiltersSchema());
    }

    protected function getData(): array
    {
        $trendConfig = $this->getTrendConfig();

        $created = Trend::model(Ranking::class)
            ->between(start: $trendConfig['start'], end: $trendConfig['end'])
            ->{$trendConfig['period']}()
            ->count();

        $deleted = Trend::query(Ranking::onlyTrashed())
            ->between(start: $trendConfig['start'], end: $trendConfig['end'])
            ->{$trendConfig['period']}()
            ->count();

        $completed = Trend::query(Ranking::query()->whereNotNull('completed_at'))
            ->between(start: $trendConfig['start'], end: $trendConfig['end'])
            ->{$trendConfig['period']}()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Rankings Created',
                    'data' => $created->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#86efac',
                ],
                [
                    'label' => 'Rankings Deleted',
                    'data' => $deleted->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f87171',
                ],
                [
                    'label' => 'Rankings Completed',
                    'data' => $completed->map(fn(TrendValue $value) => $value->aggregate),
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