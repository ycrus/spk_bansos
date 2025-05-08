<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CriteriaResource\Pages;
use App\Models\Criteria;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class CriteriaResource extends Resource
{
    protected static ?string $model = Criteria::class;

    public static ?string $pluralModelLabel = 'Criteria';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                TextInput::make('description'),
                // TextInput::make('unit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),

                TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),


            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListCriterias::route('/'),
            'create' => Pages\CreateCriteria::route('/create'),
            'edit' => Pages\EditCriteria::route('/{record}/edit'),
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
