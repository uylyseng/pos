<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WidgetSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure widget QuickActions is registered for the dashboards
        if (!DB::table('widgets')->where('widget_id', 'quick_actions')->exists()) {
            DB::table('widgets')->insert([
                'widget_id' => 'quick_actions',
                'widget_data' => json_encode([
                    'widget' => 'App\\Filament\\Widgets\\QuickActions',
                    'width' => 'full',
                    'height' => 'auto',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ensure other widgets are also registered
        $widgets = [
            'stats_overview' => 'App\\Filament\\Widgets\\StatsOverview',
            'revenue_chart' => 'App\\Filament\\Widgets\\RevenueChart',
            'latest_orders' => 'App\\Filament\\Widgets\\LatestOrders',
            'inventory_status' => 'App\\Filament\\Widgets\\InventoryStatus',
            'todays_sales' => 'App\\Filament\\Widgets\\TodaysSales',
        ];

        foreach ($widgets as $id => $class) {
            if (!DB::table('widgets')->where('widget_id', $id)->exists()) {
                DB::table('widgets')->insert([
                    'widget_id' => $id,
                    'widget_data' => json_encode([
                        'widget' => $class,
                        'width' => 'full',
                        'height' => 'auto',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
