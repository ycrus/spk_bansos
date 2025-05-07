<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParameterResource\Pages;
use App\Filament\Resources\ParameterResource\RelationManagers;
use App\Models\Criteria;
use App\Models\Parameter;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParameterResource extends Resource
{
    protected static ?string $model = Parameter::class;


    protected static ?string $navigationIcon = 'heroicon-c-adjustments-vertical';
    protected static ?string $navigationLabel = 'Parameter';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                TextInput::make('description'),
                TextInput::make('parameter_weight')
                    ->numeric(),
                // Select::make('operation')
                //     ->options([
                //         'Kurang Dari' => 'Kurang Dari',
                //         'Kurang Dari Sama Dengan' => 'Kurang Dari Sama Dengan',
                //         'Lebih Dari Sama Dengan' => 'Lebih Dari Sama Dengan',
                //         'Lebih Dari' => 'Lebih Dari',
                //         'Sama Dengan' => 'Sama Dengan',
                //         'Sampai' => 'Sampai',
                //     ])
                //     ->native(false),
                // TextInput::make('start'),
                // TextInput::make('end'),
                // TextInput::make('unit'),

                Select::make('criteria_id')
                    ->label('Criteria')
                    ->options(Criteria::all()->pluck('title', 'id'))
                    ->searchable()
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

                // TextColumn::make('operation')
                //     ->sortable()
                //     ->weight('medium')
                //     ->alignLeft(),

                // TextColumn::make('start')
                //     ->sortable()
                //     ->weight('medium')
                //     ->alignLeft(),

                // TextColumn::make('end')
                //     ->sortable()
                //     ->weight('medium')
                //     ->alignLeft(),

                // TextColumn::make('unit')
                //     ->sortable()
                //     ->weight('medium')
                //     ->alignLeft(),

                // TextColumn::make('description')
                //     ->searchable()
                //     ->sortable()
                //     ->weight('medium')
                //     ->alignLeft(),

                TextColumn::make('parameter_weight')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),

                TextColumn::make('criteria.title')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListParameters::route('/'),
            'create' => Pages\CreateParameter::route('/create'),
            'edit' => Pages\EditParameter::route('/{record}/edit'),
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
