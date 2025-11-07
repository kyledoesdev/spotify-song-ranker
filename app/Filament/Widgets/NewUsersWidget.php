<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class NewUsersWidget extends ChartWidget
{
    protected ?string $heading = 'New Users Widget';

    protected function getData(): array
    {
        $newUsers = Trend::model(User::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $deletedUsers = Trend::query(User::onlyTrashed())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $newUsers->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#93c5fd'
                ],
                [
                    'label' => 'Deleted Users',
                    'data' => $deletedUsers->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#f87171'
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
