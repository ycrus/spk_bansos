<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CriteriaResource\Pages;
use App\Filament\Resources\CriteriaResource\Pages\ViewCriterias;
use App\Models\Criteria;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;

class CriteriaResource extends Resource
{
    protected static ?string $model = Criteria::class;

    public static ?string $pluralModelLabel = 'Criteria';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                ->disabled(),
                TextInput::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Criteria::query()
                        ->orderByRaw('COALESCE(updated_at, created_at) DESC'))
            ->recordUrl(fn ($record) => ViewCriterias::getUrl(['record' => $record]))
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

                ToggleColumn::make('is_active')
                    ->label('Status'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                ->label('Status')
                ->options([
                    '1' => 'Aktif',
                    '0' => 'Tidak Aktif',
                ]),
            ])
            ->actions([
                EditAction::make()
                ->button(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCriterias::route('/'),
            'create' => Pages\CreateCriteria::route('/create'),
            'edit' => Pages\EditCriteria::route('/{record}/edit'),
            'view' => Pages\ViewCriterias::route('/{record}'),
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
