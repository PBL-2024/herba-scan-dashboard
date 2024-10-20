<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TanamanTogaResource\Pages;
use App\Filament\Resources\TanamanTogaResource\RelationManagers;
use App\Models\TanamanToga;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TanamanTogaResource extends Resource
{
    protected static ?string $model = TanamanToga::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Tanaman Toga';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Tanaman')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('manfaat')
                    ->label('Manfaat')
                    ->required(),
                Forms\Components\RichEditor::make('pengolahan')
                    ->label('Pengolahan')
                    ->required(),
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
            'index' => Pages\ListTanamanTogas::route('/'),
            'create' => Pages\CreateTanamanToga::route('/create'),
            'edit' => Pages\EditTanamanToga::route('/{record}/edit'),
        ];
    }
}
