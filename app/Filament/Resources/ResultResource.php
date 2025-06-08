<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers\NilaiAkhirRelationManager;
use App\Filament\Resources\ResultResource\RelationManagers\NilaiParameterRelationManager;
use App\Filament\Resources\ResultResource\RelationManagers\NilaiUtilityRelationManager;
use App\Filament\Resources\ResultResource\RelationManagers\RankingRelationManager;
use App\Filament\Resources\ResultResource\RelationManagers\ResultRelationManager;
use App\Models\Penilaian;
use App\Models\Period;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResultResource extends Resource
{
    protected static ?string $model = Penilaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Hasil Penilaian';
    public static ?string $pluralModelLabel = 'Hasil Penilaian';
    public static function getNavigationGroup(): ?string
    {
        return 'Result';
    }
    public static function getNavigationSort(): int
    {
        return 3;
    }

    public static function getBreadcrumb(): string
    {
        return 'Result';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('period_id')
                    ->label('Period')
                    ->relationship('period', 'name')
                    ->options(Period::where('status', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('jumlah_penerima'),
                TextInput::make('status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Penilaian::query()
                ->where('status', 'Done')
                        ->orderByRaw('created_at DESC'))
            ->columns([
                TextColumn::make('period.name'),
                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft()
                    ->dateTime('d/m/Y'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'success',
                        'Review' => 'warning',
                        'Done' => 'primary',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->button(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            NilaiParameterRelationManager::class,
            NilaiUtilityRelationManager::class,
            NilaiAkhirRelationManager::class,
            RankingRelationManager::class,
            ResultRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
            'view' => Pages\ViewResult::route('/{record}'),
            'edit' => Pages\EditResult::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole(['Super Admin', 'Admin Kecamatan', 'Staff Kecamatan']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(['Super Admin', 'Admin Kecamatan', 'Staff Kecamatan']);
    }
}
