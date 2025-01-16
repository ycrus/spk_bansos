<?php

namespace App\Filament\Resources\ResultResource\RelationManagers;

use App\Filament\Exports\NilaiUtilityExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NilaiUtilityRelationManager extends RelationManager
{
    protected static string $relationship = 'nilaiUtility';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('utility')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nilai_utilities')
            ->columns([
                TextColumn::make('penerima.nik')
                    ->label('NIK')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('pekerjaan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('penghasilan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('status_tempat_tinggal')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('status_perkawinan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('jumlah_tanggungan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('status_tempat_tinggal')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('keadaan_rumah')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('disabilitas')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('pendidikan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('fasilitas_mck')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('bahan_bakar_harian')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('kepemilikan_kendaraan')
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
                    ->exporter(NilaiUtilityExporter::class)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
