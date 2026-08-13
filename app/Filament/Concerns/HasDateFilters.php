<?php

namespace App\Filament\Concerns;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/** Shared date filtering for the dashboard trend widgets. */
trait HasDateFilters
{
    use HasFiltersSchema;

    private const MONTH_LABELS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    private const QUICK_FILTERS = [
        'all' => 'All Time',
        'day' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'year' => 'This Year',
        'custom' => 'Custom Range',
    ];

    /** Deferring keeps the filter popover open: adjusting a filter costs no round trip. */
    protected bool $hasDeferredFilters = true;

    /** Polling re-renders the widget, which closes the filter popover mid-use. */
    protected function getPollingInterval(): ?string
    {
        return null;
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components($this->getDateFiltersSchema());
    }

    public function getTrendConfig(): array
    {
        $filter = $this->filters['filter'] ?? 'all';
        $customStart = $this->filters['customStart'] ?? null;
        $customEnd = $this->filters['customEnd'] ?? null;

        if ($filter === 'custom' && filled($customStart) && filled($customEnd)) {
            return $this->buildCustomRangeConfig($customStart, $customEnd);
        }

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
                ->selectablePlaceholder(false),
            Grid::make(2)->schema([
                DatePicker::make('customStart')
                    ->label('From')
                    ->maxDate(now())
                    ->visibleJs(<<<'JS'
                        $get('filter') === 'custom'
                        JS),
                DatePicker::make('customEnd')
                    ->label('To')
                    ->maxDate(now())
                    ->visibleJs(<<<'JS'
                        $get('filter') === 'custom'
                        JS),
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
        $start = $now->copy()->startOfWeek();
        $end = $now->copy()->endOfWeek();

        return $this->config($start, $end, 'perDay', $this->dateRangeLabels($start, $end, 'D'));
    }

    private function buildMonthConfig(): array
    {
        $now = $this->userNow();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        return $this->config($start, $end, 'perDay', $this->dateRangeLabels($start, $end, 'j'));
    }

    private function buildYearConfig(): array
    {
        $now = $this->userNow();

        return $this->config($now->copy()->startOfYear(), $now->copy()->endOfYear(), 'perMonth', self::MONTH_LABELS);
    }

    private function buildAllTimeConfig(): array
    {
        $tz = $this->userTimezone();
        $oldest = User::query()->min('created_at');
        $start = $oldest
            ? Carbon::parse($oldest)->tz($tz)->startOfMonth()
            : now()->tz($tz)->startOfYear();
        $end = now()->tz($tz)->endOfMonth();

        if ($start->diffInMonths($end, absolute: true) > 24) {
            $yearStart = $start->copy()->startOfYear();
            $yearEnd = $end->copy()->endOfYear();

            return $this->config($yearStart, $yearEnd, 'perYear', $this->dateRangeLabels($yearStart, $yearEnd, 'Y', '1 year'));
        }

        return $this->config($start, $end, 'perMonth', $this->dateRangeLabels($start, $end, 'M Y', '1 month'));
    }

    /** Avoids perWeek: PHP uses ISO weeks and MySQL uses %u, so labels desync from the data. */
    private function buildCustomRangeConfig(string $customStart, string $customEnd): array
    {
        $tz = $this->userTimezone();
        $start = Carbon::parse($customStart, $tz)->startOfDay();
        $end = Carbon::parse($customEnd, $tz)->endOfDay();

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        if ($start->isSameDay($end)) {
            return $this->config($start, $end, 'perHour', $this->hourLabels());
        }

        $days = $start->diffInDays($end, absolute: true);

        [$period, $labels] = match (true) {
            $days <= 31 => ['perDay', $this->dateRangeLabels($start, $end, 'j')],
            $days <= 365 => ['perDay', $this->dateRangeLabels($start, $end, 'M j')],
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
        return Auth::user()->timezone;
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
}
