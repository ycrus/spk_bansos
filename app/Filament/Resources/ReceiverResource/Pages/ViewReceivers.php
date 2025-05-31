<?php

namespace App\Filament\Resources\ReceiverResource\Pages;

use App\Filament\Resources\ReceiverResource;
use App\Filament\Resources\UserManagementResource;
use App\Models\CalonPenerima;
use App\Models\Penilaian;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action as PageAction;

class ViewReceivers extends ViewRecord
{
    protected static string $resource = ReceiverResource::class;



public function getHeaderActions(): array
{
    return [
        PageAction::make('approveOrReject')
            ->label('Approval')
            ->icon('heroicon-o-check-circle')
            ->visible(fn () => $this->record->status === 'Need Approval')
            ->form([
                Radio::make('status')
                    ->options([
                        'Approved' => 'Approve',
                        'Rejected' => 'Reject',
                    ])
                    ->required()
                    ->reactive() 
                    ->afterStateUpdated(fn ($state, callable $set) => $state !== 'Rejected' ? $set('note', null) : null)
                    ->inline(),

                Textarea::make('remark')
                    ->label('Catatan')
                    ->visible(fn (callable $get) => $get('status') === 'Rejected')
                    ->dehydrated() 
                    ->default(null),
            ])
            ->action(function (array $data, $record): void {
                if ($data['status'] === 'Approved') {
                    $penilaians = Penilaian::where('status', 'Active')->get();

                    foreach ($penilaians as $penilaian) {
                        CalonPenerima::firstOrCreate([
                            'receiver_id' => $record->id,
                            'penilaian_id' => $penilaian->id,
                        ]);
                    }
                }

                $this->record->update([
                    'status' => $data['status'],
                    'remark' => $data['remark'] ?? null,
                    'note' => $data['note'] ?? null,
                ]);

                Notification::make()
                    ->title('Status berhasil diperbarui.')
                    ->success()
                    ->send();
            }),
    ];
}

}