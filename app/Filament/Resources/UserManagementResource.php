<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserManagementResource\Pages;
use App\Filament\Resources\UserManagementResource\RelationManagers;
use App\Models\User;
use App\Models\Roles;
use App\Models\UserManagement;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash; 

class UserManagementResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),

                TextInput::make('email'),

                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn (string $context) => $context === 'create')
                    ->visible(fn (string $context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->confirmed(),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(fn (string $context) => $context === 'create')
                    ->visible(fn (string $context) => $context === 'create'),

                Select::make('role')
                    ->label('Role')
                    ->options(Roles::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Not Active',
                    ])
                    ->default(true) // Set default value to Active (true)
                    ->required(),
                //
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('userRole.name')
                ->label('Role'),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Not Active')
                    ->sortable(),
                //
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
            'index' => Pages\ListUserManagement::route('/'),
            'create' => Pages\CreateUserManagement::route('/create'),
            'edit' => Pages\EditUserManagement::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(['Super Admin']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(['Super Admin']);
    }

}
