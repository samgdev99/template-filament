<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Seguridad';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('activo')->default(true)->inline(false)->required(),
                Grid::make(2)->schema([
                    TextInput::make('name')->label('Nombre')->required(),
                    TextInput::make('email')->label('Correo electrónico')->required()->email(),
                    TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $context) => $context === 'create')
                        ->visible(fn (string $context) => $context === 'edit' || $context === 'create')
                        ->maxLength(255),
                    TextInput::make('email_verified_at')
                        ->label('Correo verificado')
                        ->disabled()
                        ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y H:i') : 'No verificado')
                        ->visible(fn (string $context) => in_array($context, ['edit', 'view'])),
                ]),
                CheckboxList::make('roles')
                    ->relationship('roles', 'name')
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                ToggleColumn::make('activo'),
                TextColumn::make('roles.name'),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return 'Usuarios';
    }

    public static function getModelLabel(): string
    {
        return 'usuario';
    }

    public static function getPluralModelLabel(): string
    {
        return 'usuarios';
    }
}
