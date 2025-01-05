<?php

namespace App\Filament\Resources\PenilaianResource\RelationManagers;

use App\Models\Receiver;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceiverPeriodRelationManager extends RelationManager
{
    protected static string $relationship = 'dataPenerima';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('receiver_id')
                    ->label('Penerima')
                    ->options(Receiver::all()->pluck('nama', 'id'))
                    ->searchable()
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
                TextColumn::make('penerima.tanggal_lahir')
                    ->label("Tanggal Lahir")
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
