<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Filament\Support\RawJs;
class BestSaleProduct extends ChartWidget
{
    protected ?string $heading = 'Best Sale Product';

    // 1. Set a default filter value so the chart loads properly on first visit
    public ?string $filter = '5';

    protected function getData(): array
    {
        // 2. Get the active filter value (cast it to an integer)
        $limit = (int) $this->filter;

        // Fetch products dynamically based on the dropdown filter selection
        $bestSellers = Product::query()
            ->withSum('orderDetails as total_sales', 'quantity')
            ->orderByDesc('total_sales')
            ->limit($limit) // Dynamic limit applied here!
            ->get();

        // 3. Optional: Dynamic color generator so you don't run out of colors if they choose Top 20
        $colors = $this->getDynamicColors($limit);

        return [
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $bestSellers->pluck('total_sales')->map(fn($value) => (int) $value)->toArray(),
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $bestSellers->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    // 4. Define the dropdown filter options
    protected function getFilters(): ?array
    {
        return [
            '5' => 'Top 5',
            '10' => 'Top 10',
        ];
    }

    // Helper method to ensure we have enough colors if the user selects Top 10 or 20
    private function getDynamicColors(int $count): array
    {
        $palette = ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40', '#34d399', '#fb7185', '#a78bfa'];

        // If the requested count is larger than our palette, repeat the colors
        return array_slice(array_merge($palette, $palette), 0, $count);
    }
    protected function getOptions(): RawJs|array
    {
        return RawJs::from("
        {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            
                            // Calculate percentage on the fly natively
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