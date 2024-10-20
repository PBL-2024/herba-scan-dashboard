<?php

namespace App\Filament\Resources\TanamanTogaResource\Pages;

use App\Filament\Resources\TanamanTogaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTanamanTogas extends ListRecords
{
    protected static string $resource = TanamanTogaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
