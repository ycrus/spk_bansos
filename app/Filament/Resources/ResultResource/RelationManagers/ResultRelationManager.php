<?php

namespace App\Filament\Resources\ResultResource\RelationManagers;

use App\Filament\Exports\ResultExporter;
use App\Models\Result;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultRelationManager extends RelationManager
{
    protected static string $relationship = 'result';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('result')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('result')
            ->columns([
                Tables\Columns\TextColumn::make('penerima.nik')
                    ->searchable()
                    ->sortable()
                    ->label('NIK'),
                Tables\Columns\TextColumn::make('penerima.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                Tables\Columns\TextColumn::make('penerima.desa.name')
                    ->searchable()
                    ->sortable()
                    ->label('Kelurahan'),
                BooleanColumn::make('is_ranked')
                    ->label('Status')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                ExportAction::make()
                    ->label('Download Result Data')
                    ->exporter(ResultExporter::class),
                
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
