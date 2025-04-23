<?php

namespace App\Filament\Resources\ToppingResource\Pages;

use App\Filament\Resources\ToppingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTopping extends CreateRecord
{
    protected static string $resource = ToppingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
