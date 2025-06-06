<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianResource\Pages;
use App\Filament\Resources\PenilaianResource\RelationManagers\CalonPenerimaRelationManager;
use App\Filament\Resources\PenilaianResource\RelationManagers\ReceiverPeriodRelationManager;
use App\Models\CalonPenerima;
use App\Models\Penilaian;
use App\Models\Period;
use App\Models\Receiver;
use App\Services\PenilaianService;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                    ->options(Period::where('status', 'Active')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            Period::where('id', $state)->update(['status' => 'Review']);
                        }
                    }),
                TextInput::make('jumlah_penerima')
                ->required(),
                TextInput::make('status')
                    ->default("Active")
                    ->disabled()
                    ->dehydrated(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period.name')
                    ->sortable(),
                TextColumn::make('jumlah_penerima')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Review' => 'warning',
                        'Done' => 'primary',
                    }),
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

                        Period::where('id', $record->period_id)->update(['status' => 'Done']);

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
            ReceiverPeriodRelationManager::class,
            CalonPenerimaRelationManager::class
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
        return auth()->user()?->hasRole(['Super Admin','Admin Kecamatan','Staff Kecamatan']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(['Super Admin','Admin Kecamatan','Staff Kecamatan']);
    }

    public static function afterCreate(Model $record): void
    {
        $approvedAlternatives = Receiver::where('status', 'Approved')->get();

        foreach ($approvedAlternatives as $alternative) {
            CalonPenerima::create([
                'penilaian_id' => $record->id,
                'receiver_id' => $alternative->id,
            ]);
        }
    }
}
