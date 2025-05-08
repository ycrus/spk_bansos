<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Pages\Actions\CreateAction;
use Filament\Actions\Action;

class CreateProgram extends CreateRecord
{
    protected static string $resource = ProgramResource::class;

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
