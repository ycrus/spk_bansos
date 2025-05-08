<?php

namespace App\Filament\Resources\ReceiverResource\Pages;

use App\Filament\Resources\ReceiverResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\CreateAction;
use Filament\Actions\Action;

class CreateReceiver extends CreateRecord
{
    protected static string $resource = ReceiverResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Create')
                ->submit('create')
                ->color('primary'),
                
            Action::make('cancel')
                ->label('Cancel')
                ->color('danger')
                ->url($this->getResource()::getUrl())
                // ->icon('heroicon-m-x-mark')
                ,
        ];
    }

}
