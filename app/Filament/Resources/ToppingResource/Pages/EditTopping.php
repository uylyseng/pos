<?php

namespace App\Filament\Resources\ToppingResource\Pages;

use App\Filament\Resources\ToppingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTopping extends EditRecord
{
    protected static string $resource = ToppingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
