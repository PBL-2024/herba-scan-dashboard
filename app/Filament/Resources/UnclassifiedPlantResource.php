<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnclassifiedPlantResource\Pages;
use App\Filament\Resources\UnclassifiedPlantResource\RelationManagers;
use App\Models\UnclassifiedPlant;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
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
                TextInput::make('nama')
                    ->label('Nama Tanaman')
                    ->required(),
                FileUpload::make('file')
                    ->label('Gambar')
                    ->image()
                    ->directory('unclassified_plants')
                    ->required(),
                Select::make('is_verified')
                    ->label('Terverifikasi')
                    ->options([
                        1 => 'Ya',
                        0 => 'Tidak',
                    ])
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('is_verified')
                    ->label('Terverifikasi')
                    ->formatStateUsing(fn($state) => $state ? 'Ya' : 'Tidak')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable(),

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
            ])
            ->poll(10);
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
