<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserPlatformWidget extends ChartWidget
{
    protected ?string $heading = 'User Platforms';
    protected ?string $maxHeight = '300px';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $platforms = User::query()
            ->select('user_platform', 'user_agent')
            ->whereNotNull('user_platform')
            ->whereNotNull('user_agent')
            ->where('user_platform', '!=', '')
            ->get()
            ->map(function (User $user) {
                $platform = strtolower(trim($user->user_platform, " \t\n\r\0\x0B\""));

                if (str_contains($user->user_agent ?? '', 'iPhone')) {
                    return 'iPhone';
                }

                return $platform;
            })
            ->countBy()
            ->sortDesc();

        $colors = $platforms->keys()->map(fn ($platform) => $this->getPlatformColor($platform))->values();

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $platforms->values()->toArray(),
                    'backgroundColor' => $colors->toArray(),
                ],
            ],
            'labels' => $platforms->keys()->map(fn ($p) => $this->formatLabel($p))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getPlatformColor(string $platform): string
    {
        return match ($platform) {
            'iphone' => '#f4f4f5',
            'macos' => '#7dd3fc',
            'android' => '#a3e635',
            'windows' => '#2dd4bf',
            'linux' => '#facc15',
            'chrome os', 'chromeos' => '#fb923c',
            default => '#a1a1aa',
        };
    }

    protected function formatLabel(string $platform): string
    {
        return match ($platform) {
            'iphone' => 'iPhone',
            'macos' => 'macOS',
            'android' => 'Android',
            'windows' => 'Windows',
            'linux' => 'Linux',
            'chrome os', 'chromeos' => 'Chrome OS',
            default => ucfirst($platform),
        };
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}