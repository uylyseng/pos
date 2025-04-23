<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '200px';

    protected int | string | array $columnSpan = 1;

    protected static string $view = 'filament.widgets.quick-actions-widget';
}
