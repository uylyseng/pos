<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\LatestOrdersWidget;
use App\Filament\Widgets\SalesChartWidget;
use App\Filament\Widgets\QuickActionsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class Dashboard extends BaseDashboard
{
    // Use protected to override the parent class's property
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            QuickActionsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): array
    {
        return [
            'default' => 2,
            'sm' => 2,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
            '2xl' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            SalesChartWidget::class,
            // LatestOrdersWidget::class,
            // AccountWidget::class,
            // FilamentInfoWidget::class,
        ];
    }

    public function getWidgetsColumns(): array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
            '2xl' => 2,
        ];
    }
}
