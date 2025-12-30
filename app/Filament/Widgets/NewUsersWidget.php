<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDateFilters;
use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class NewUsersWidget extends ChartWidget
{
    use HasDateFilters;
    use HasFiltersSchema;

    protected ?string $heading = 'New Users Widget';

    protected static ?int $sort = 2;

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components($this->getDateFiltersSchema());
    }

    protected function getData(): array
    {
        $trendConfig = $this->getTrendConfig();

        $newUsers = Trend::model(User::class)
            ->between(start: $trendConfig['start'], end: $trendConfig['end'])
            ->{$trendConfig['period']}()
            ->count();

        $deletedUsers = Trend::query(User::onlyTrashed())
            ->between(start: $trendConfig['start'], end: $trendConfig['end'])
            ->{$trendConfig['period']}()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $newUsers->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#93c5fd',
                ],
                [
                    'label' => 'Deleted Users',
                    'data' => $deletedUsers->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f87171',
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