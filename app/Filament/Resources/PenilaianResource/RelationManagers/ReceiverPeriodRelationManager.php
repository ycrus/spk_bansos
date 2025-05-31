<?php

namespace App\Filament\Resources\PenilaianResource\RelationManagers;

use App\Models\Calculate_Receiver;
use App\Models\CalonPenerima;
use App\Models\Receiver;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\ValidationException;

class ReceiverPeriodRelationManager extends RelationManager
{
    protected static string $relationship = 'dataPenerima';
    protected static ?string $title = 'Data Calon Penerima Bantuan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('receiver_id')
                    ->label('Penerima')
                    ->multiple()
                    ->options(
                        function () {
                            $parentModel = $this->getOwnerRecord();
                            $usedReceiverIds = $parentModel->dataPenerima()->pluck('receiver_id')->toArray() ?? [];
                
                            return Receiver::whereNotIn('id', $usedReceiverIds)
                            ->where('status', 'Approved')
                                ->get()
                                ->mapWithKeys(function ($receiver) {
                                    return [$receiver->id => "{$receiver->nik} - {$receiver->nama} - {$receiver->desa->name}"];
                                });
                        }
                    )
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->searchable(),

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
                // Tables\Actions\CreateAction::make()
                // ->label('Add')
                // ->icon('heroicon-o-plus')
                // ->color('success')
                // ->mutateFormDataUsing(function (array $data): array {
                //     return $data;
                // })
                // ->using(function ($data, $livewire) {
                //     $relationship = $livewire->getRelationship();

                //     if (!$relationship) {
                //         throw new \Exception("Relationship not found!");
                //     }

                //     $parent = $livewire->ownerRecord;
                //     $errors = [];
                //     $validReceiverIds = [];

                //     foreach ($data['receiver_id'] as $receiverId) {

                //         $exists = $relationship
                //             ->where('receiver_id', $receiverId)
                //             ->where($relationship->getForeignKeyName(), $parent->getKey())
                //             ->exists();

                //         if ($exists) {
                //             $receiver = Receiver::find($receiverId);
                //             $errors[] = $receiver?->nama;
                //         } else {
                //             $validReceiverIds[] = $receiverId;
                //         }
                //     }

                //     if (!empty($errors)) {
                //         $list = implode(', ', array_map(fn($n) => "'$n'", $errors));
                            
                //         Notification::make()
                //             ->title("Data {$list} sudah ditambahkan.")
                //             ->danger()
                //             ->send();

                //             $errorMessage = "Penerima {$list} sudah ditambahkan.";
    
                //             throw ValidationException::withMessages([
                //                 'receiver_id' => $errorMessage,
                //             ]);
                //     }

                //     // Simpan semua yang valid
                //     foreach ($validReceiverIds as $receiverId) {
                //         $created = $relationship->create([
                //             'receiver_id' => $receiverId,
                //         ]);
                //     }
        
                //     return $created; // Hindari simpan default
                // }),
            ])
            ->actions([
                Action::make('delete')
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->color('danger') // 'success', 'danger', 'warning', 'secondary', 'gray', 'info'
                ->iconPosition('before') 
                ->button() 
                ->action(function (array $data, $record) {
                    $exists = CalonPenerima::where('penilaian_id', $record->penilaian_id)
                    ->where('receiver_id', $record->receiver_id)
                    ->exists();

                    if (! $exists) {
                        CalonPenerima::create([
                            'penilaian_id' => $record->penilaian_id,
                            'receiver_id' => $record->receiver_id,
                        ]);

                        Calculate_Receiver::where('penilaian_id', $record->penilaian_id)
                            ->where('receiver_id', $record->receiver_id)
                            ->delete();
                    }
                })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
