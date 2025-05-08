<?php

namespace App\Filament\Resources\PeriodResource\Pages;

use App\Filament\Resources\PeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\CreateAction;
use Filament\Actions\Action;

class CreatePeriod extends CreateRecord
{
    protected static string $resource = PeriodResource::class;

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
