<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsWidget extends BaseWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $salesToday = Sale::whereDate('created_at', today())->count();
        $revenueToday = Sale::whereDate('created_at', today())
            ->where('status', 'confirmed')
            ->sum('total');
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();

        return [
            Stat::make('Sales Today', $salesToday)
                ->description('Orders today')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Revenue Today', '$'.number_format($revenueToday, 2))
                ->description('Confirmed sales')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Total Products', $totalProducts)
                ->description('In inventory')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Low Stock', $lowStockProducts)
                ->description('Need restocking')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockProducts > 0 ? 'danger' : 'success'),
        ];
    }
}
