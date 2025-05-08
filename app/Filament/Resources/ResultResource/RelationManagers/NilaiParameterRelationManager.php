<?php

namespace App\Filament\Resources\ResultResource\RelationManagers;

use App\Filament\Exports\NilaiParameterExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NilaiParameterRelationManager extends RelationManager
{
    protected static string $relationship = 'nilaiParameter';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('parameter')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nilai_bobots')
            ->columns([
                TextColumn::make('penerima.nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('pekerjaan')
                ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignCenter(),
                TextColumn::make('penghasilan')
                    // ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('status_perkawinan')
                // ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('jumlah_tanggungan')
                // ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('status_tempat_tinggal')
                // ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('keadaan_rumah')
                // ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('disabilitas')
                // ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('pendidikan')
                // ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('fasilitas_mck')
                ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('bahan_bakar_harian')
                // ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('kepemilikan_kendaraan')
                // ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                                ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(NilaiParameterExporter::class)
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
