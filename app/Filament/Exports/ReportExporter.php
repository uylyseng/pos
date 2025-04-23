<?php

namespace App\Filament\Exports;

use App\Models\Report;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ReportExporter extends Exporter
{
    protected static ?string $model = Report::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('date')
                ->label('Date'),
            ExportColumn::make('total_orders')
                ->label('Total Orders'),
            ExportColumn::make('total_sales')
                ->label('Total Sales'),
            ExportColumn::make('average_order_value')
                ->label('Average Order Value'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your report has been exported successfully.';
    }
}
