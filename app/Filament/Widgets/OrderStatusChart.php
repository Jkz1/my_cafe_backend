<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected ?string $heading = 'Order Status';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $statuses = ['pending', 'shipping', 'completed', 'cancelled'];
        $data = [];
        $labels = [];

        foreach ($statuses as $status) {
            $count = Order::where('status', $status)->count();
            $data[] = $count;
            $labels[] = ucfirst($status);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                    'backgroundColor' => [
                        '#f59e0b', // pending — amber
                        '#3b82f6', // shipping — blue
                        '#10b981', // completed — emerald
                        '#ef4444', // cancelled — red
                    ],
                    'borderColor' => '#1f2937',
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): RawJs|array
    {
        return RawJs::from("
            {
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let dataset = context.chart.data.datasets[0];
                                let total = dataset.data.reduce((acc, curr) => acc + curr, 0);
                                let pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        ");
    }
}
