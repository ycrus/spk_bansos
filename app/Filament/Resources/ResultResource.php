<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Filament\Resources\ResultResource\RelationManagers\NilaiAkhirRelationManager;
use App\Filament\Resources\ResultResource\RelationManagers\NilaiParameterRelationManager;
use App\Filament\Resources\ResultResource\RelationManagers\NilaiUtilityRelationManager;
use App\Filament\Resources\ResultResource\RelationManagers\RankingRelationManager;
use App\Filament\Resources\ResultResource\RelationManagers\ResultRelationManager;
use App\Models\Penilaian;
use App\Models\Period;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResultResource extends Resource
{
    protected static ?string $model = Penilaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Result';

    public static function getBreadcrumb(): string
    {
        return 'Result';
    }

    public static ?string $pluralModelLabel = 'Result';

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
            ->columns([
                TextColumn::make('period.name'),
                TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
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
            // 'create' => Pages\CreateResult::route('/create'),
            'view' => Pages\ViewResult::route('/{record}'),
            'edit' => Pages\EditResult::route('/{record}/edit'),
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
