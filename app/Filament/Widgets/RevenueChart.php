<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public ?string $filter = '6';

    protected function getFilters(): ?array
    {
        return [
            '6' => __('widgets.revenue.last_6_months'),
            '12' => __('widgets.revenue.last_12_months'),
        ];
    }

    public function getHeading(): string
    {
        return __('widgets.revenue.heading');
    }

    protected function getData(): array
    {
        $months = (int) $this->filter;

        $labels = [];
        $totals = [];
        $paid = [];

        foreach (range($months - 1, 0) as $offset) {
            $month = Carbon::now()->subMonths($offset);
            $labels[] = $month->translatedFormat('M Y');

            $query = Invoice::query()
                ->whereNotIn('status', [InvoiceStatus::Draft->value, InvoiceStatus::Cancelled->value])
                ->whereYear('invoice_date', $month->year)
                ->whereMonth('invoice_date', $month->month);

            $totals[] = round((float) (clone $query)->sum('grand_total'), 2);
            $paid[] = round((float) (clone $query)->sum('amount_paid'), 2);
        }

        return [
            'datasets' => [
                [
                    'label' => __('widgets.revenue.total_invoice'),
                    'data' => $totals,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.6)',
                    'borderColor' => '#f59e0b',
                ],
                [
                    'label' => __('widgets.revenue.paid'),
                    'data' => $paid,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                    'borderColor' => '#10b981',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
