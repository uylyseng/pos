<?php

namespace App\Filament\Resources\ToppingResource\Pages;

use App\Filament\Resources\ToppingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListToppings extends ListRecords
{
    protected static string $resource = ToppingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
