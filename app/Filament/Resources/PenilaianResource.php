<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianResource\Pages;
use App\Filament\Resources\PenilaianResource\Pages\ListPenilaians;
use App\Filament\Resources\PenilaianResource\RelationManagers;
use App\Filament\Resources\PenilaianResource\RelationManagers\ReceiverPeriodRelationManager;
use App\Models\Penilaian;
use App\Models\Period;
use App\Models\Receiver;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;

class PenilaianResource extends Resource
{
    protected static ?string $model = Penilaian::class;

    protected static ?string $navigationIcon = 'heroicon-m-numbered-list';

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
                        // Update the selected period to set its status to false
                        if ($state) {
                            Period::where('id', $state)->update(['status' => false]);
                        }
                    }),
                TextInput::make('jumlah_penerima')->required(),
                // TextInput::make('status')->default("Active")->required(),

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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status === 'Active'),
                Action::make('startButton')
                    ->label('START')
                    // ->url(fn($record) => "localhost:8080/api/spk/calculate/start/{$record->id}")
                    // ->url(fn($record) => config('services.api.surrounding_url') . "/api/spk/calculate/start/{$record->id}")
                    ->action(function ($record) {
                        // Lakukan aksi seperti memanggil API atau kalkulasi
                        Http::get('http://localhost:8080/api/spk/calculate/start/' . $record->id);

                        // Aksi setelah kalkulasi, bisa langsung refresh halaman atau beri notifikasi
                        session()->flash('success', 'Calculation started');
                    })
                    ->color('red')
                    ->icon('heroicon-o-link')
                    ->visible(fn($record) => $record->status === 'Active'),
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
}
