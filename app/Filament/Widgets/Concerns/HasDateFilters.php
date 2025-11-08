<?php

namespace App\Filament\Widgets\Concerns;

trait HasDateFilters
{
    public function getTrendConfig(string $filter): array
    {
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
                'labels' => $this->getDaysOfMonthLabels($now),
            ],
            'year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
                'period' => 'perMonth',
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ],
        };
    }

    private function getHourLabels(): array
    {
        $labels = [];

        for ($i = 0; $i < 24; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
        }

        return $labels;
    }

    private function getDaysOfMonthLabels(): array
    {
        $daysInMonth = now()->tz(auth()->user()->timezone)->daysInMonth;

        $labels = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = (string) $i;
        }

        return $labels;
    }
}