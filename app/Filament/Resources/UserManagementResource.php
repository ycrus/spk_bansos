<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserManagementResource\Pages;
use App\Filament\Resources\UserManagementResource\RelationManagers;
use App\Models\User;
use App\Models\Roles;
use App\Models\Kelurahan;
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
use Filament\Pages\Actions;

class UserManagementResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->rule(function ($livewire) {
                        return function (string $attribute, $value, $fail) use ($livewire) {
                            $userId = $livewire->record?->id;

                            $exists = User::where('email', $value)
                                ->where('status', true)
                                ->when($userId, fn ($query) => $query->where('id', '!=', $userId))
                                ->exists();

                            if ($exists) {
                                $fail('Sudah ada user aktif dengan email ini.');
                            }
                        };
                    }),

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
                    ->required()
                    ->reactive(),

                Select::make('desa')
                    ->label('Desa')
                    ->options(Kelurahan::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->visible(fn ($get) => in_array($get('role'), [4]))
                    ->rule(function ($get, $livewire) {
                        return function ($attribute, $value, $fail) use ($livewire) {
                            $userId = $livewire->record?->id;

                            $existing = User::where('desa', $value)
                                ->where('status', true)
                                ->when($userId, fn ($query) => $query->where('id', '!=', $userId)) 
                                ->exists();
                
                            if ($existing) {
                                $fail('Sudah ada user aktif untuk desa ini.');
                            }
                        };
                    }),

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
                TextColumn::make('desaStaf.name')
                ->label('Desa'), 
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

    public static function create(Request $request)
    {
        // Membuat record baru
        $record = parent::create($request);

        // Redirect ke halaman index setelah create berhasil
        return redirect()->route('filament.resources.usermanagement.index');
    }

}
