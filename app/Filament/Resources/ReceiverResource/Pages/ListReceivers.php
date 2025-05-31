<?php

namespace App\Filament\Resources\ReceiverResource\Pages;

use App\Filament\Resources\ReceiverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListReceivers extends ListRecords
{
    protected static string $resource = ReceiverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Data Alternatif'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),

            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'Approved')),

            'need' => Tab::make('Need Approval')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'Need Approval')),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'Rejected')),
        ];

        if (auth()->user()?->hasRole(['Staff Desa'])) {
            $tabs['draft'] = Tab::make('Draft')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'Draft'));
        }

        return $tabs;
    }
}
