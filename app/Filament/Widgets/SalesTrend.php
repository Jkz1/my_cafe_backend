<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema; // Using your v5 Schema class
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;

class SalesTrendChart extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    protected ?string $heading = 'Sales Revenue Trend';

    public ?array $data = [];

    public function mount(): void
    {
        // Set your default state values directly into the data array
        $this->data = [
            'startDate' => now()->month(4)->startOfMonth()->toDateString(), // 2026-04-01
            'endDate' => now()->month(4)->endOfMonth()->toDateString(),     // 2026-04-30
        ];
    }

    /**
     * V5 Schema hook instead of form()
     */
    public function schema(Schema $schema): Schema
    {
        return $schema
            ->statePath('data') // Binds inputs directly to our $data array
            ->components([
                DatePicker::make('startDate')
                    ->label('Start')
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn() => $this->dispatch('updateChartData')),
                DatePicker::make('endDate')
                    ->label('End')
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(fn() => $this->dispatch('updateChartData')),
            ])
            ->columns(2);
    }
    protected function getData(): array
    {
        $startInput = $this->data['startDate'] ?? now()->subDays(30)->toDateString();
        $endInput = $this->data['endDate'] ?? now()->toDateString();

        $start = Carbon::parse($startInput)->startOfDay();
        $end = Carbon::parse($endInput)->endOfDay();

        $salesData = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('SUM(total_price) as total_revenue, DATE(created_at) as date_key')
            ->groupBy('date_key')
            ->pluck('total_revenue', 'date_key')
            ->all();

        $labels = [];
        $chartData = [];
        $daysDifference = $start->diffInDays($end);

        for ($i = 0; $i <= $daysDifference; $i++) {
            $currentDate = $start->copy()->addDays($i);
            $dateString = $currentDate->format('Y-m-d');

            $labels[] = $currentDate->format('M d');
            $chartData[] = (float) ($salesData[$dateString] ?? 0.0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $chartData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::from("
            {
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) { return '$' + value.toLocaleString(); }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: $' + (context.parsed.y || 0).toLocaleString();
                            }
                        }
                    }
                }
            }
        ");
    }
}