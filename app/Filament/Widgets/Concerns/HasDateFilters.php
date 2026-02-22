<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

trait HasDateFilters
{
    private const MONTH_LABELS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    private const DAY_LABELS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    private const QUICK_FILTERS = [
        'all' => 'All Time',
        'day' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'year' => 'This Year',
    ];

    public function getTrendConfig(): array
    {
        $customStart = $this->filters['customStart'] ?? null;
        $customEnd = $this->filters['customEnd'] ?? null;

        if ($customStart && $customEnd) {
            return $this->buildCustomRangeConfig($customStart, $customEnd);
        }

        $filter = $this->filters['filter'] ?? 'all';

        return match ($filter) {
            'day' => $this->buildDayConfig(),
            'week' => $this->buildWeekConfig(),
            'month' => $this->buildMonthConfig(),
            'year' => $this->buildYearConfig(),
            default => $this->buildAllTimeConfig(),
        };
    }

    protected function getDateFiltersSchema(): array
    {
        return [
            Select::make('filter')
                ->label('Quick Filter')
                ->options(self::QUICK_FILTERS)
                ->default('all')
                ->live()
                ->afterStateUpdated(function (Set $set) {
                    $this->filters['customStart'] = null;
                    $this->filters['customEnd'] = null;
                    $this->dispatch('$refresh');
                }),
            Grid::make(2)->schema([
                DatePicker::make('customStart')
                    ->label('From')
                    ->maxDate(now()),
                DatePicker::make('customEnd')
                    ->label('To')
                    ->maxDate(now()),
            ]),
        ];
    }

    // -- Config Builders -------------------------------------------------------

    private function buildDayConfig(): array
    {
        $now = $this->userNow();

        return $this->config($now->copy()->startOfDay(), $now->copy()->endOfDay(), 'perHour', $this->hourLabels());
    }

    private function buildWeekConfig(): array
    {
        $now = $this->userNow();

        return $this->config($now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'perDay', self::DAY_LABELS);
    }

    private function buildMonthConfig(): array
    {
        $now = $this->userNow();

        return $this->config(
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth(),
            'perDay',
            $this->daysInMonthLabels($now->daysInMonth),
        );
    }

    private function buildYearConfig(): array
    {
        $now = $this->userNow();

        return $this->config($now->copy()->startOfYear(), $now->copy()->endOfYear(), 'perMonth', self::MONTH_LABELS);
    }

    private function buildAllTimeConfig(): array
    {
        $tz = $this->userTimezone();
        $start = User::query()->oldest()->first()?->created_at?->tz($tz)?->startOfMonth()
            ?? now()->tz($tz)->startOfYear();
        $end = now()->tz($tz)->endOfMonth();

        if ($start->diffInMonths($end) > 24) {
            $yearStart = $start->copy()->startOfYear();
            $yearEnd = $end->copy()->endOfYear();

            return $this->config($yearStart, $yearEnd, 'perYear', $this->dateRangeLabels($yearStart, $yearEnd, 'Y', '1 year'));
        }

        return $this->config($start, $end, 'perMonth', $this->dateRangeLabels($start, $end, 'M Y', '1 month'));
    }

    private function buildCustomRangeConfig(string $customStart, string $customEnd): array
    {
        $tz = $this->userTimezone();
        $start = Carbon::parse($customStart, $tz)->startOfDay();
        $end = Carbon::parse($customEnd, $tz)->endOfDay();
        $days = $start->diffInDays($end);

        [$period, $labels] = match (true) {
            $days <= 1 => ['perHour', $this->hourLabels()],
            $days <= 31 => ['perDay', $this->dateRangeLabels($start, $end, 'j')],
            $days <= 365 => ['perWeek', $this->dateRangeLabels($start, $end, 'M j', '1 week')],
            default => ['perMonth', $this->dateRangeLabels($start, $end, 'M Y', '1 month')],
        };

        return $this->config($start, $end, $period, $labels);
    }

    // -- Helpers ---------------------------------------------------------------

    private function config(Carbon $start, Carbon $end, string $period, array $labels): array
    {
        return compact('start', 'end', 'period', 'labels');
    }

    private function userNow(): Carbon
    {
        return now()->tz($this->userTimezone());
    }

    private function userTimezone(): string
    {
        return auth()->user()->timezone;
    }

    private function dateRangeLabels(Carbon $start, Carbon $end, string $format, string $interval = '1 day'): array
    {
        return collect(CarbonPeriod::create($start, $interval, $end))
            ->map(fn (Carbon $date) => $date->format($format))
            ->toArray();
    }

    private function hourLabels(): array
    {
        return collect(range(0, 23))
            ->map(fn (int $hour) => Str::padLeft($hour, 2, '0').':00')
            ->toArray();
    }

    private function daysInMonthLabels(int $daysInMonth): array
    {
        return collect(range(1, $daysInMonth))
            ->map(fn (int $day) => (string) $day)
            ->toArray();
    }
}