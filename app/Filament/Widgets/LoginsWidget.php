<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Kyledoesdev\Essentials\Stats\LoginStat;

class LoginsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $timezone = auth()->user()->timezone;
        
        return [
            Stat::make('Daily Active Users', LoginStat::query()
                ->start(now()->tz($timezone)->startOfDay())
                ->end(now()->tz($timezone)->endOfDay())
                ->get()
                ->sum('increments')
            ),
            Stat::make('Weekly Active Users', LoginStat::query()
                ->start(now()->tz($timezone)->subWeek()->startOfWeek())
                ->end(now()->tz($timezone)->subWeek()->endOfWeek())
                ->get()
                ->sum('increments')
            ),
            Stat::make('Monthly Active Users', LoginStat::query()
                ->start(now()->tz($timezone)->subMonth()->startOfMonth())
                ->end(now()->tz($timezone)->subMonth()->endOfMonth())
                ->get()
                ->sum('increments')
            ),
        ];
    }
}
