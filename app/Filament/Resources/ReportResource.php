<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use App\Models\Order;
use App\Models\Payment;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Filament\Exports\ReportExporter;
use Filament\Actions\Exports\ExportAction;
use Filament\Actions\Exports\ExportBulkAction;

class ReportResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'date';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('report_type')
                    ->label('Report Type')
                    ->options([
                        'daily' => 'Daily Report',
                        'monthly' => 'Monthly Report',
                        'yearly' => 'Yearly Report',
                        'custom' => 'Custom Date Range',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->visible(fn (callable $get) => $get('report_type') === 'daily'),
                Forms\Components\DatePicker::make('month')
                    ->label('Month')
                    ->required()
                    ->visible(fn (callable $get) => $get('report_type') === 'monthly'),
                Forms\Components\Select::make('year')
                    ->label('Year')
                    ->options(array_combine(range(date('Y'), date('Y') - 5), range(date('Y'), date('Y') - 5)))
                    ->required()
                    ->visible(fn (callable $get) => $get('report_type') === 'yearly'),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required()
                    ->visible(fn (callable $get) => $get('report_type') === 'custom'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->required()
                    ->visible(fn (callable $get) => $get('report_type') === 'custom'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_orders')
                    ->label('Total Orders')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('Total Sales')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_order_value')
                    ->label('Average Order Value')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_sales')
                    ->label('Daily Sales')
                    ->money('USD')
                    ->sortable()
                    ->visible(fn () => request()->get('report_type') === 'daily'),
                Tables\Columns\TextColumn::make('monthly_sales')
                    ->label('Monthly Sales')
                    ->money('USD')
                    ->sortable()
                    ->visible(fn () => request()->get('report_type') === 'monthly'),
                Tables\Columns\TextColumn::make('yearly_sales')
                    ->label('Yearly Sales')
                    ->money('USD')
                    ->sortable()
                    ->visible(fn () => request()->get('report_type') === 'yearly'),
            ])
            ->defaultSort('date', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('daily')
                    ->label('Daily')
                    ->url(fn () => static::getUrl('index', ['report_type' => 'daily']))
                    ->color(fn () => request()->get('report_type') === 'daily' ? 'primary' : 'gray'),
                Tables\Actions\Action::make('monthly')
                    ->label('Monthly')
                    ->url(fn () => static::getUrl('index', ['report_type' => 'monthly']))
                    ->color(fn () => request()->get('report_type') === 'monthly' ? 'primary' : 'gray'),
                Tables\Actions\Action::make('yearly')
                    ->label('Yearly')
                    ->url(fn () => static::getUrl('index', ['report_type' => 'yearly']))
                    ->color(fn () => request()->get('report_type') === 'yearly' ? 'primary' : 'gray'),
                Tables\Actions\Action::make('custom')
                    ->label('Custom Range')
                    ->url(fn () => static::getUrl('index', ['report_type' => 'custom']))
                    ->color(fn () => request()->get('report_type') === 'custom' ? 'primary' : 'gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $reportType = request()->get('report_type', 'daily');
        $date = request()->get('date');
        $month = request()->get('month');
        $year = request()->get('year');
        $startDate = request()->get('start_date');
        $endDate = request()->get('end_date');

        $query = Order::query();

        // Apply date filters based on report type
        switch ($reportType) {
            case 'daily':
                if ($date) {
                    $query->whereDate('created_at', $date);
                } else {
                    $query->whereDate('created_at', today());
                }
                break;
            case 'monthly':
                if ($month) {
                    $query->whereYear('created_at', Carbon::parse($month)->year)
                        ->whereMonth('created_at', Carbon::parse($month)->month);
                } else {
                    $query->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month);
                }
                break;
            case 'yearly':
                if ($year) {
                    $query->whereYear('created_at', $year);
                } else {
                    $query->whereYear('created_at', now()->year);
                }
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                break;
        }

        // Select and group by date
        $query->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total) as total_sales'),
            DB::raw('AVG(total) as average_order_value'),
            DB::raw('DATE(created_at) as id')
        )
        ->groupBy('date');

        // Add period-specific sales
        switch ($reportType) {
            case 'daily':
                $query->addSelect(DB::raw('SUM(total) as daily_sales'));
                break;
            case 'monthly':
                $query->addSelect(DB::raw('SUM(total) as monthly_sales'));
                break;
            case 'yearly':
                $query->addSelect(DB::raw('SUM(total) as yearly_sales'));
                break;
        }

        return $query;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'export',
        ];
    }
}
