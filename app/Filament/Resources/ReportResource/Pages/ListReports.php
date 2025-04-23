<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Report')
                ->icon('heroicon-o-building-library')
                ->action(function () {
                    $query = $this->getResource()::getEloquentQuery();
                    $records = $query->get();
                    $reportType = request()->get('report_type', 'daily');

                    $csv = Writer::createFromString('');

                    // Define headers based on report type
                    $headers = ['Date', 'Total Orders', 'Total Sales', 'Average Order Value'];
                    if ($reportType === 'daily') {
                        $headers[] = 'Daily Sales';
                    } elseif ($reportType === 'monthly') {
                        $headers[] = 'Monthly Sales';
                    } elseif ($reportType === 'yearly') {
                        $headers[] = 'Yearly Sales';
                    }

                    $csv->insertOne($headers);

                    foreach ($records as $record) {
                        $row = [
                            $record->date->format('Y-m-d'),
                            $record->total_orders,
                            $record->total_sales,
                            $record->average_order_value,
                        ];

                        if ($reportType === 'daily') {
                            $row[] = $record->daily_sales;
                        } elseif ($reportType === 'monthly') {
                            $row[] = $record->monthly_sales;
                        } elseif ($reportType === 'yearly') {
                            $row[] = $record->yearly_sales;
                        }

                        $csv->insertOne($row);
                    }

                    $filename = 'report-' . now()->format('Y-m-d-His') . '.csv';
                    Storage::put('public/reports/' . $filename, $csv->toString());

                    return response()->download(
                        Storage::path('public/reports/' . $filename),
                        $filename
                    )->deleteFileAfterSend();
                }),
        ];
    }
}
