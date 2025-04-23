<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Overview';

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        // Get data for the last 7 days
        $data = $this->getSalesData();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $data['sales'],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'borderWidth' => 2,
                    'fill' => 'start',
                ],
                [
                    'label' => 'Orders',
                    'data' => $data['orders'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 2,
                    'fill' => 'start',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getSalesData(): array
    {
        // Get the last 7 days
        $dates = collect();
        $sales = collect();
        $orders = collect();

        // Create a collection of the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates->push($date->format('M d'));

            // Sales amount for each day
            $salesAmount = Order::whereDate('created_at', $date)->sum('total') ?? 0;
            $sales->push($salesAmount);

            // Orders count for each day
            $ordersCount = Order::whereDate('created_at', $date)->count() ?? 0;
            $orders->push($ordersCount);
        }

        return [
            'labels' => $dates->toArray(),
            'sales' => $sales->toArray(),
            'orders' => $orders->toArray(),
        ];
    }
}
