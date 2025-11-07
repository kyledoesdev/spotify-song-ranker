<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use App\Stats\LoginStat;
use Carbon\Carbon;

class LoginsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Logins Today', LoginStat::query()
                ->start(now()->startOfDay())
                ->end(now()->endOfDay())
                ->get()
                ->sum('increments')
            ),
            Stat::make('Logins Last Week', LoginStat::query()
                ->start(now()->subMonth()->startOfMonth())
                ->end(now()->subMonth()->endOfMonth())
                ->get()
                ->sum('increments')
            ),
            Stat::make('Logins Last Month', LoginStat::query()
                ->start(now()->subWeek()->startOfWeek())
                ->end(now()->subWeek()->endOfWeek())
                ->get()
                ->sum('increments')
            ),
        ];
    }
}
