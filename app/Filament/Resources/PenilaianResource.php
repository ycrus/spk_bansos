<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianResource\Pages;
use App\Filament\Resources\PenilaianResource\RelationManagers\ReceiverPeriodRelationManager;
use App\Models\Penilaian;
use App\Models\Period;
use App\Services\PenilaianService;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenilaianResource extends Resource
{
    protected static ?string $model = Penilaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('period_id')
                    ->label('Period')
                    ->relationship('period', 'name')
                    ->options(Period::where('status', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            Period::where('id', $state)->update(['status' => false]);
                        }
                    }),
                TextInput::make('jumlah_penerima')->required(),
                TextInput::make('status')->default("Active")->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period.name'),
                TextColumn::make('jumlah_penerima'),
                TextColumn::make('status'),
            ])
            ->recordUrl(function ($record) {
                return $record->status === 'Active'
                    ? static::getUrl('edit', ['record' => $record])
                    : null;
            })
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status === 'Active'),
                Action::make('startButton')
                    ->label('START')
                    ->action(function ($record) {
                        $service = app(PenilaianService::class);
                        $service->startCalculate($record->id);

                        $record->status = 'Done';
                        $record->save();

                        session()->flash('success', 'Calculation started');
                    })                    
                    ->color('red')
                    ->icon('heroicon-o-link')
                    ->visible(fn($record) => $record->status === 'Active' && $record->dataPenerima()->count() > 0),
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
            ReceiverPeriodRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenilaians::route('/'),
            'create' => Pages\CreatePenilaian::route('/create'),
            'edit' => Pages\EditPenilaian::route('/{record}/edit'),
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
