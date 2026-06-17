<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Filament\Support\RawJs;

class BestSaleProduct extends ChartWidget
{
    protected ?string $heading = 'Best Selling Products';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '280px';


    public ?string $filter = '5';

    protected function getData(): array
    {

        $limit = (int) $this->filter;


        $bestSellers = Product::query()
            ->withSum('orderDetails as total_sales', 'quantity')
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get();


        $colors = $this->getDynamicColors($limit);

        return [
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $bestSellers->pluck('total_sales')->map(fn($value) => (int) $value)->toArray(),
                    'backgroundColor' => $colors,
                    'borderColor' => '#1f2937',
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $bestSellers->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }


    protected function getFilters(): ?array
    {
        return [
            '5' => 'Top 5',
            '10' => 'Top 10',
        ];
    }


    private function getDynamicColors(int $count): array
    {
        $palette = [
            '#8b5cf6',
            '#f59e0b',
            '#06b6d4',
            '#ec4899',
            '#10b981',
            '#ef4444',
            '#3b82f6',
            '#f97316',
            '#14b8a6',
            '#a855f7',
        ];


        return array_slice(array_merge($palette, $palette), 0, $count);
    }

    protected function getOptions(): RawJs|array
    {
        return RawJs::from("
        {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;

                            
                            let dataset = context.chart.data.datasets[0];
                            let total = dataset.data.reduce((acc, current) => acc + current, 0);
                            let percentage = parseFloat((value / total * 100).toFixed(1));

                            if (label) {
                                label += ': ';
                            }
                            return label + value + ' units (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    ");
    }
}