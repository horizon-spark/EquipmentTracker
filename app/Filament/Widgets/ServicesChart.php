<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\ChartWidget;

class ServicesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Обслуживания по месяцам';

    protected ?string $description = 'Динамика за последние 6 месяцев';

    protected ?string $maxHeight = '22rem';

    protected string $color = 'primary';

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $monthsAgo) => now()->subMonthsNoOverflow($monthsAgo)->startOfMonth());

        return [
            'datasets' => [
                [
                    'label' => 'Обслуживания',
                    'data' => $months
                        ->map(fn ($month): int => Service::query()
                            ->whereBetween('service_date', [
                                $month->copy()->startOfMonth(),
                                $month->copy()->endOfMonth(),
                            ])
                            ->count())
                        ->all(),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.14)',
                    'pointBackgroundColor' => '#6366f1',
                    'pointBorderColor' => '#ffffff',
                    'pointRadius' => 4,
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $months
                ->map(fn ($month): string => $month->locale('ru')->translatedFormat('M Y'))
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
