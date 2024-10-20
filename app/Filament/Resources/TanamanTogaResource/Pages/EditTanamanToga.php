<?php

namespace App\Filament\Resources\TanamanTogaResource\Pages;

use App\Filament\Resources\TanamanTogaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTanamanToga extends EditRecord
{
    protected static string $resource = TanamanTogaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
