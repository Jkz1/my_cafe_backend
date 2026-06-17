<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -3;

    protected function getStats(): array
    {

        $totalRevenue = Order::sum('total_price');
        $revenueThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');


        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenueChart[] = (float) Order::whereDate('created_at', $date)->sum('total_price');
        }


        $totalOrders = Order::count();
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();


        $ordersChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $ordersChart[] = Order::whereDate('created_at', $date)->count();
        }


        $totalCustomers = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super admin']);
        })->count();
        $newCustomersThisMonth = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super admin']);
        })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();


        $productsInStock = Product::where('is_available', true)->count();
        $totalProducts = Product::count();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('$' . number_format($revenueThisMonth, 2) . ' this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($revenueChart),

            Stat::make('Total Orders', number_format($totalOrders))
                ->description($ordersThisMonth . ' this month')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart($ordersChart),

            Stat::make('Total Customers', number_format($totalCustomers))
                ->description($newCustomersThisMonth . ' new this month')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([2, 4, 6, 3, 5, 7, $totalCustomers]),

            Stat::make('Products in Stock', $productsInStock . ' / ' . $totalProducts)
                ->description($productsInStock . ' available')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning')
                ->chart([3, 5, 4, 6, 8, 7, $productsInStock]),
        ];
    }
}
