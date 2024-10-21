<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlantResource\Pages;
use App\Filament\Resources\PlantResource\RelationManagers;
use App\Models\Plant;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PlantResource extends Resource
{
    protected static ?string $model = Plant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Tanaman Toga';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Tanaman')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('cover')
                    ->label('Cover')
                    ->required()
                    ->image()
                    ->directory('tanaman-toga')
                    ->visibility('public'),
                Section::make()
                    ->schema([
                        TinyEditor::make('deskripsi')
                            ->label('Deksripsi')
                            ->fileAttachmentsDirectory('tanaman-toga')
                            ->required(),
                        TinyEditor::make('manfaat')
                            ->label('Manfaat')
                            ->fileAttachmentsDirectory('tanaman-toga')
                            ->required(),
                        TinyEditor::make('pengolahan')
                            ->label('Pengolahan')
                            ->fileAttachmentsDirectory('tanaman-toga')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama') // Tampilkan nama tanaman di tabel
                    ->label('Nama Tanaman')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('cover')
                    ->label('Cover')
                    ->size(100),
                TextColumn::make('user.name') // Tampilkan nama tanaman di tabel
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at') // Tampilkan nama tanaman di tabel
                    ->label('Created At')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListPlants::route('/'),
            'create' => Pages\CreatePlant::route('/create'),
            'edit' => Pages\EditPlant::route('/{record}/edit'),
        ];
    }
}
