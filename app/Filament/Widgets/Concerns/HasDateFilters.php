<?php

namespace App\Filament\Widgets\Concerns;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\DatePicker;

trait HasDateFilters
{
    public function getTrendConfig(): array
    {
        $filter = $this->filterFormData['filter'] ?? 'year';
        $customStart = $this->filterFormData['customStart'] ?? null;
        $customEnd = $this->filterFormData['customEnd'] ?? null;

        if ($customStart && $customEnd) {
            return $this->getCustomRangeConfig($customStart, $customEnd);
        }

        $now = now()->tz(auth()->user()->timezone);

        return match($filter) {
            'day' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'period' => 'perHour',
                'labels' => $this->getHourLabels(),
            ],
            'week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
                'period' => 'perDay',
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            ],
            'month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'period' => 'perDay',
                'labels' => $this->getDaysOfMonthLabels(),
            ],
            'year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
                'period' => 'perMonth',
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ],
        };
    }

    private function getCustomRangeConfig(string $customStart, string $customEnd): array
    {
        $tz = auth()->user()->timezone;
        $start = Carbon::parse($customStart, $tz)->startOfDay();
        $end = Carbon::parse($customEnd, $tz)->endOfDay();
        $days = $start->diffInDays($end);

        if ($days <= 1) {
            return [
                'start' => $start,
                'end' => $end,
                'period' => 'perHour',
                'labels' => $this->getHourLabels(),
            ];
        }

        if ($days <= 31) {
            return [
                'start' => $start,
                'end' => $end,
                'period' => 'perDay',
                'labels' => $this->getDateRangeLabels($start, $end, 'j'),
            ];
        }

        if ($days <= 365) {
            return [
                'start' => $start,
                'end' => $end,
                'period' => 'perWeek',
                'labels' => $this->getDateRangeLabels($start, $end, 'M j', '1 week'),
            ];
        }

        return [
            'start' => $start,
            'end' => $end,
            'period' => 'perMonth',
            'labels' => $this->getDateRangeLabels($start, $end, 'M Y', '1 month'),
        ];
    }

    private function getDateRangeLabels(Carbon $start, Carbon $end, string $format, string $interval = '1 day'): array
    {
        return collect(CarbonPeriod::create($start, $interval, $end))
            ->map(fn(Carbon $date) => $date->format($format))
            ->toArray();
    }

    protected function getDateFiltersSchema(): array
    {
        return [
            Select::make('filter')
                ->label('Quick Filter')
                ->options([
                    'day' => 'Today',
                    'week' => 'This Week',
                    'month' => 'This Month',
                    'year' => 'This Year',
                ])
                ->default('year'),
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

    private function getHourLabels(): array
    {
        return collect(range(0, 23))
            ->map(fn($hour) => Str::padLeft($hour, 2, '0') . ':00')
            ->toArray();
    }

    private function getDaysOfMonthLabels(): array
    {
        return collect(range(1, now()->tz(auth()->user()->timezone)->daysInMonth))
            ->map(fn($day) => (string) $day)
            ->toArray();
    }
}