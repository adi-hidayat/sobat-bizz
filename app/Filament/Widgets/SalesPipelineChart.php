<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class SalesPipelineChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): string
    {
        return __('widgets.pipeline.heading');
    }

    protected function getData(): array
    {
        $counts = Lead::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [];
        $data = [];

        foreach (LeadStatus::cases() as $status) {
            $labels[] = $status->getLabel();
            $data[] = (int) ($counts[$status->value] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => __('widgets.pipeline.dataset'),
                    'data' => $data,
                    'backgroundColor' => [
                        '#3b82f6', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
