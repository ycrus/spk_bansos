<?php

namespace App\Filament\Resources\PenilaianResource\Pages;

use App\Filament\Resources\PenilaianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\CreateAction;
use Filament\Actions\Action;

class CreatePenilaian extends CreateRecord
{
    protected static string $resource = PenilaianResource::class;

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

    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }
}
