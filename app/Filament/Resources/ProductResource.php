<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductSize;
use App\Models\Topping;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use GuzzleHttp\Promise\TaskQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Repeater;

class ProductResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    protected static ?string $navigationBadgeTooltip = 'The number of products in the system.';

    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Tabs::make('Product Details')
                ->tabs([
                Tabs\Tab::make('General Information')
                    ->schema([
                    Forms\Components\Section::make('Product Information')
                        ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                            Forms\Components\Group::make()
                                ->schema([
                                    Forms\Components\TextInput::make('name_km')
                                        ->label('Name (Khmer)')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Enter name in Khmer'),
                                    Forms\Components\TextInput::make('name_en')
                                        ->label('Name (English)')
                                        ->maxLength(255)
                                        ->placeholder('Enter name in English'),
                                    Forms\Components\Toggle::make('is_active')
                                        ->label('Active')
                                        ->required()
                                        ->inline(false),
                                    Forms\Components\Select::make('category_id')
                                        ->label('Category')
                                        ->relationship('category', 'name_km')
                                        ->required(),
                                    Forms\Components\TextInput::make('base_price')
                                        ->label('Base Price')
                                        ->required()
                                        ->numeric()
                                        ->placeholder('Enter base price'),
                                        Forms\Components\Textarea::make('description')
                                        ->label('Description')
                                        ->placeholder('Enter product description')
                                        ->rows(5),
                                    ]),
                                    Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Product Image')
                                            ->image()
                                            ->directory('products')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->imageResizeMode('cover')        // Crop to fit
                                            ->imageCropAspectRatio('1:1')     // Force 1:1 aspect ratio
                                            ->imageResizeTargetWidth('400')   // Target width
                                            ->imageResizeTargetHeight('400')  // Target height
                                            ->imageResizeUpscale(false)       // Don't upscale small images
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(1024)                   // 1MB max size
                                            ->helperText('Upload a square image (1:1 ratio). Recommended size: 400x400 pixels.'),
                                        Forms\Components\Toggle::make('has_sizes')
                                            ->label('Has Sizes')
                                            ->required()
                                            ->reactive()
                                            ->inline(false),
                                        Forms\Components\Toggle::make('has_toppings')
                                            ->label('Has Toppings')
                                            ->required()
                                            ->reactive()
                                            ->inline(false),
                                        Forms\Components\Toggle::make('is_stock')
                                            ->label('In Stock')
                                            ->required()
                                            ->inline(false)
                                            ->reactive()
                                            ->afterStateUpdated(fn (callable $set, $state) => $set('quantity', $state ? 0 : null)),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->required(fn (callable $get) => $get('is_stock'))
                                            ->numeric()
                                            ->default(0)
                                            ->placeholder('Enter quantity')
                                            ->visible(fn (callable $get) => $get('is_stock')),
                                        Forms\Components\TextInput::make('low_stock_threshold')
                                            ->label('Low Stock Threshold')
                                            ->required(fn (callable $get) => $get('is_stock'))
                                            ->numeric()
                                            ->default(10)
                                            ->placeholder('Enter low stock threshold')
                                            ->visible(fn (callable $get) => $get('is_stock')),
                                ]),
                            ]),
                        ])->columnSpanFull(),
                    Forms\Components\Hidden::make('created_by')
                        ->default(fn() => Auth::id()),
                    Forms\Components\Hidden::make('updated_by')
                        ->default(fn() => Auth::id()),
                    ])->columnSpanFull(),

                Tabs\Tab::make('Sizes')
                    ->visible(fn (callable $get) => $get('has_sizes'))
                    ->schema([
                        Forms\Components\Section::make('Product Sizes')
                            ->description('Add different sizes and their prices for this product')
                            ->schema([
                                Forms\Components\Repeater::make('productSizes')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('size_id')
                                            ->label('Size')
                                            ->options(function () {
                                                return Size::where('is_active', true)
                                                    ->get()
                                                    ->pluck('name_km', 'id')
                                                    ->toArray();
                                            })
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $set, $state) {
                                                $size = Size::find($state);
                                                if ($size) {
                                                    $set('size_name_en', $size->name_en);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('size_name_en')
                                            ->label('Size Name (English)')
                                            ->disabled()
                                            ->dehydrated(false),
                                        Forms\Components\TextInput::make('multiplier')
                                            ->label('Multiplier')
                                            ->required()
                                            ->numeric()
                                            ->placeholder('Enter price for this size'),
                                        Forms\Components\Hidden::make('created_by')
                                            ->default(fn() => Auth::id()),
                                        Forms\Components\Hidden::make('updated_by')
                                            ->default(fn() => Auth::id()),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->createItemButtonLabel('Add Size')
                            ])
                    ]),

                Tabs\Tab::make('Toppings')
                    ->visible(fn (callable $get) => $get('has_toppings'))
                    ->schema([
                        Forms\Components\Section::make('Product Toppings')
                            ->description('Add toppings and their prices for this product')
                            ->schema([
                                Forms\Components\Repeater::make('productToppings')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('topping_id')
                                            ->label('Topping')
                                            ->options(function () {
                                                return Topping::where('is_active', true)
                                                    ->get()
                                                    ->pluck('name_km', 'id')
                                                    ->toArray();
                                            })
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function (callable $set, $state) {
                                                $topping = Topping::find($state);
                                                if ($topping) {
                                                    $set('topping_name_en', $topping->name_en);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('topping_name_en')
                                            ->label('Topping Name (English)')
                                            ->disabled()
                                            ->dehydrated(false),
                                        Forms\Components\TextInput::make('price')
                                            ->label('Price')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->placeholder('Enter additional price for this topping'),
                                        Forms\Components\Hidden::make('created_by')
                                            ->default(fn() => Auth::id()),
                                        Forms\Components\Hidden::make('updated_by')
                                            ->default(fn() => Auth::id()),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->createItemButtonLabel('Add Topping')
                            ])
                    ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                // Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name_km')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name_km')
                    ->label('Category')
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_sizes')
                    ->label('Sizes')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_toppings')
                    ->label('Toppings')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_stock')
                    ->label('Stock')
                    ->boolean(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Products')
                    ->trueLabel('Active Products')
                    ->falseLabel('Inactive Products')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_active', true),
                        false: fn (Builder $query) => $query->where('is_active', false),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Stock Status')
                    ->options([
                        'low_stock' => 'Low Stock',
                        'in_stock' => 'In Stock',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) {
                            return $query;
                        }

                        if ($data['value'] === 'low_stock') {
                            return $query->where('is_stock', true)
                                ->whereColumn('quantity', '<', 'low_stock_threshold');
                        }

                        if ($data['value'] === 'in_stock') {
                            return $query->where('is_stock', true)
                                ->where('quantity', '>', 0);
                        }

                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ReplicateAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->filtersFormColumns(3);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'restore',
            'replicate',
        ];
    }
}
