<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Get the start of today
        $today = Carbon::today();

        // Get today's orders count
        $todayOrdersCount = Order::whereDate('created_at', $today)->count() ?? 0;

        // Get today's sales total
        $todaySalesTotal = Order::whereDate('created_at', $today)->sum('total') ?? 0;

        // Get total products
        $totalProducts = Product::count() ?? 0;

        // Get total users
        $totalUsers = User::count() ?? 0;

        return [
            Stat::make('Today\'s Orders', $todayOrdersCount)
                ->description('Number of orders today')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, $todayOrdersCount]),

            Stat::make('Today\'s Sales', '$' . number_format($todaySalesTotal, 2))
                ->description('Total sales today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart([15, 8, 12, 10, 15, 10, $todaySalesTotal]),

            Stat::make('Products', $totalProducts)
                ->description('Total products in inventory')
                ->descriptionIcon('heroicon-m-cube')
                ->color('danger'),

            Stat::make('Users', $totalUsers)
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
