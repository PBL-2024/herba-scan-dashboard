<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image_url')
                    ->label('Profile')
                    ->image()
                    ->directory('unclassified_plants')
                    ->required(),
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->placeholder('John Doe'),
                TextInput::make('email')
                    ->email()
                    ->label('Email')
                    ->required()
                    ->placeholder('johndoe@example.com'),
                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required()
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->placeholder('********'),
                Select::make('roles')
                    ->label('Role')
                    ->multiple() // Allow selecting multiple roles
                    ->relationship('roles', 'name') // Mengambil nama role dari relasi
                    ->options(Role::all()->pluck('name', 'id')) // Menampilkan semua role yang tersedia
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                BadgeColumn::make('roles.name')->label('Role'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
