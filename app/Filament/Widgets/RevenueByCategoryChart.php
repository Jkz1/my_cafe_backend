<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\OrderDetails;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueByCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Revenue by Category';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $categoryRevenue = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(order_details.unit_price * order_details.quantity) as total_revenue'))
            ->groupBy('categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        $colors = [
            '#8b5cf6', // violet
            '#06b6d4', // cyan
            '#f59e0b', // amber
            '#ec4899', // pink
            '#10b981', // emerald
            '#ef4444', // red
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $categoryRevenue->pluck('total_revenue')->map(fn($v) => round((float) $v, 2))->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $categoryRevenue->count()),
                    'borderRadius' => 6,
                    'borderSkipped' => false,
                ],
            ],
            'labels' => $categoryRevenue->pluck('category_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs|array
    {
        return RawJs::from("
            {
                indexAxis: 'y',
                scales: {
                    x: {
                        ticks: {
                            callback: function(value) { return '$' + value.toLocaleString(); }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: $' + (context.parsed.x || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        ");
    }
}
