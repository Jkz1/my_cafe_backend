<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BestSaleProduct;
use App\Filament\Widgets\LatestOrders;
use App\Filament\Widgets\OrderStatusChart;
use App\Filament\Widgets\RevenueByCategoryChart;
use App\Filament\Widgets\SalesTrendChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{

    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            SalesTrendChart::class,
            OrderStatusChart::class,
            RevenueByCategoryChart::class,
            BestSaleProduct::class,
            LatestOrders::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 3,
        ];
    }
}
