<?php

namespace App\Filament\Resources\PenilaianResource\RelationManagers;

use App\Models\Calculate_Receiver;
use App\Models\CalonPenerima;
use App\Models\Receiver;
use Filament\Tables\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CalonPenerimaRelationManager extends RelationManager
{
    protected static string $relationship = 'dataCalonPenerima';

    protected static ?string $title = 'Data Alternatif';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Receiver')
            ->columns([
                TextColumn::make('penerima.nik')
                    ->label("NIK")
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('penerima.nama')
                    ->label("Nama")
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('penerima.desa.name')
                    ->label("Desa")
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('penerima.alamat')
                    ->label("Alamat")
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
            ])
            ->filters([
                //
            ])
            ->headerActions([
               
            ])
            ->actions([
                Action::make('add')
                ->label('ADD')
                ->icon('heroicon-o-plus-circle')
                ->color('primary') // 'success', 'danger', 'warning', 'secondary', 'gray', 'info'
                ->iconPosition('before') 
                ->button() 
                ->action(function ($record) {
                    $exists = Calculate_Receiver::where('penilaian_id', $record->penilaian_id)
                    ->where('receiver_id', $record->receiver_id)
                    ->exists();

                    if (! $exists) {
                        Calculate_Receiver::create([
                            'penilaian_id' => $record->penilaian_id,
                            'receiver_id' => $record->receiver_id,
                        ]);

                        CalonPenerima::where('penilaian_id', $record->penilaian_id)
                            ->where('receiver_id', $record->receiver_id)
                            ->delete();
                    }
                })
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('add')
                        ->label('Add All')
                        ->icon('heroicon-o-plus-circle')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $exists = Calculate_Receiver::where('penilaian_id', $record->penilaian_id)
                                ->where('receiver_id', $record->receiver_id)
                                ->exists();

                                if (! $exists) {
                                    Calculate_Receiver::create([
                                        'penilaian_id' => $record->penilaian_id,
                                        'receiver_id' => $record->receiver_id,
                                    ]);

                                    CalonPenerima::where('penilaian_id', $record->penilaian_id)
                                        ->where('receiver_id', $record->receiver_id)
                                        ->delete();
                                }
                            }
                        }),
                ]),
            ]);
    }
}
