<?php

namespace App\Filament\Resources\UnclassifiedPlantResource\Pages;

use App\Filament\Resources\UnclassifiedPlantResource;
use App\Models\UnclassifiedPlant;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Storage;
use ZipArchive;

class ListUnclassifiedPlants extends ListRecords
{
    protected static string $resource = UnclassifiedPlantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('downloadImages')
                ->label('Download Images')
                ->action('downloadImages')
                ->icon('heroicon-o-arrow-down'),
        ];
    }

    public function downloadImages()
    {
        $zip = new ZipArchive;
        $fileName = 'unclassified-' . $this->tableSearch . '.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE) {
            // Apply filters to the query
            $query = UnclassifiedPlant::query();

            if ($this->tableFilters['verified']['isActive']) {
                $query->where('is_verified', true);
            }

            if ($this->tableFilters['unverified']['isActive']) {
                $query->where('is_verified', false);
            }

            if ($this->tableSearch) {
                $query->where('nama', 'like', '%' . $this->tableSearch . '%');
            }

            $files = $query->pluck('file');

            foreach ($files as $file) {
                $relativeName = basename($file);
                $zip->addFile(storage_path('app/public/' . $file), $relativeName);
            }

            $zip->close();
        }

        return response()->download(public_path($fileName))->deleteFileAfterSend(true);
    }
}
