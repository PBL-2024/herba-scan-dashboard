<?php

namespace App\Filament\Resources\UnclassifiedPlantResource\Pages;

use App\Filament\Resources\UnclassifiedPlantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnclassifiedPlant extends EditRecord
{
    protected static string $resource = UnclassifiedPlantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
