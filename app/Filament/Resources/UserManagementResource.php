<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserManagementResource\Pages;
use App\Models\User;
use App\Models\Roles;
use App\Models\Kelurahan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Database\Eloquent\Builder;

class UserManagementResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'User Management';
    public static function getNavigationGroup(): ?string
    {
        return 'Management';
    }
    public static function getNavigationSort(): int
    {
        return 4;
    }

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
                    ->minLength(8)
                    ->confirmed(),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(fn (string $context) => $context === 'create')
                    ->visible(fn (string $context) => $context === 'create'),

                Select::make('role')
                    ->label('Role')
                    ->options(function () {
                        if (auth()->user()?->hasRole('Admin Kecamatan')) {
                            return Roles::whereIn('name', [
                                'Staff Desa',
                                'Staff Kecamatan',
                            ])->pluck('name', 'id');
                        }
                
                        return Roles::all()->pluck('name', 'id');
                    })
                    ->searchable()
                    ->visible(fn ($record) => $record->id !== auth()->id())
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
                    ->default(true)
                    ->visible(fn ($record) => $record->id !== auth()->id())
                    ->required(),
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {

                if (auth()->user()?->hasRole('Admin Kecamatan')) {
                    $roles = ['3', '4']; 
                    return $query->whereIn('role', $roles);
                }
            
                return $query;
            })
            ->query(
                User::query()
                        ->orderByRaw('COALESCE(updated_at, created_at) DESC'))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('userRole.name')
                    ->label('Role'),
                TextColumn::make('desaStaf.name')
                    ->label('Desa'),
                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft()
                    ->dateTime('d/m/Y'),
                TextColumn::make('updated_at')
                    ->label('Modified Date')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft()
                    ->dateTime('d/m/Y'),
                ToggleColumn::make('status')
                    ->label('Status')
                    ->disabled(fn ($record) => $record->id === auth()->id()),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->button(),
            ])
            ->bulkActions([
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
        return auth()->user()?->hasRole(['Super Admin','Admin Kecamatan']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(['Super Admin','Admin Kecamatan']);
    }
}
