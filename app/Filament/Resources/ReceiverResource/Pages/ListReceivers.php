<?php

namespace App\Filament\Resources\ReceiverResource\Pages;

use App\Filament\Resources\ReceiverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

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

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Approved')),
            
            'need' => Tab::make('Need Approval')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Need Approval')),
            
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Rejected')),
        ];
    }
}
