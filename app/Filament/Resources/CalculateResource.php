<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalculateResource\Pages;
use App\Filament\Resources\CalculateResource\RelationManagers;
use App\Filament\Resources\CalculateResource\RelationManagers\CalculateReceiverRelationManager;
use App\Models\Calculate;
use App\Models\Program;
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

class CalculateResource extends Resource
{
    protected static ?string $model = Calculate::class;

    public static ?string $pluralModelLabel = 'Perhitungan';

    protected static ?string $navigationIcon = 'heroicon-c-calculator';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('batch'),

                Select::make('program_id')
                    ->label('Program')
                    ->options(Program::all()->pluck('name', 'id'))
                    ->searchable()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),

                TextColumn::make('program.name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),

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
            CalculateReceiverRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalculates::route('/'),
            'create' => Pages\CreateCalculate::route('/create'),
            'edit' => Pages\EditCalculate::route('/{record}/edit'),
        ];
    }
}
