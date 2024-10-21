<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnclassifiedPlantResource\Pages;
use App\Filament\Resources\UnclassifiedPlantResource\RelationManagers;
use App\Models\UnclassifiedPlant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnclassifiedPlantResource extends Resource
{
    protected static ?string $model = UnclassifiedPlant::class;
    protected static ?string $navigationGroup = 'Tanaman';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Belum Terklasifikasi';
    protected static ?string $label = 'Tanaman Belum Terklasifikasi ';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file')
                    ->label('Gambar')
                    ->size(200),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUnclassifiedPlants::route('/'),
            'create' => Pages\CreateUnclassifiedPlant::route('/create'),
            'edit' => Pages\EditUnclassifiedPlant::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    // add badge to show if the plant is verified or not
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_verified', false)->count();
    }
}
