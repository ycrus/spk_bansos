<?php

namespace App\Filament\Resources\ReceiverResource\Pages;

use App\Filament\Resources\ReceiverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReceivers extends ListRecords
{
    protected static string $resource = ReceiverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create') // Ubah label
                // ->icon('heroicon-o-plus') // Tambahkan ikon
                ->color('primary') // Warna tombol,,
        ];
    }
}
