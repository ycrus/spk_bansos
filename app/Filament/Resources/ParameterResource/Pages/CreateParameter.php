<?php

namespace App\Filament\Resources\ParameterResource\Pages;

use App\Filament\Resources\ParameterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\CreateAction;
use Filament\Actions\Action;

class CreateParameter extends CreateRecord
{
    protected static string $resource = ParameterResource::class;

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
