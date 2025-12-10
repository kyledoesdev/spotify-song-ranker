<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;
use App\Stats\LoginStat;
use Carbon\Carbon;

class LoginsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $timezone = auth()->user()->timezone;
        
        return [
            Stat::make('Logins Today', LoginStat::query()
                ->start(now()->tz($timezone)->startOfDay())
                ->end(now()->tz($timezone)->endOfDay())
                ->get()
                ->sum('increments')
            ),
            Stat::make('Logins Last Week', LoginStat::query()
                ->start(now()->tz($timezone)->subWeek()->startOfWeek())
                ->end(now()->tz($timezone)->subWeek()->endOfWeek())
                ->get()
                ->sum('increments')
            ),
            Stat::make('Logins Last Month', LoginStat::query()
                ->start(now()->tz($timezone)->subMonth()->startOfMonth())
                ->end(now()->tz($timezone)->subMonth()->endOfMonth())
                ->get()
                ->sum('increments')
            ),
        ];
    }
}
