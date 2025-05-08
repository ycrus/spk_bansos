<?php

namespace App\Filament\Resources\ReceiverResource\Pages;

use App\Filament\Resources\ReceiverResource;
use App\Filament\Resources\UserManagementResource;
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
                    ->inline(),

                Textarea::make('note')
                    ->label('Catatan')
                    ->visible(fn (callable $get) => $get('status') === 'Rejected'),
            ])
            ->action(function (array $data): void {
                $this->record->update([
                    'status' => $data['status'],
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