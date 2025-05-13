<?php

namespace App\Filament\Resources\ProgramResource\Pages;

use App\Filament\Resources\ProgramResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPrograms extends ViewRecord
{
    protected static string $resource = ProgramResource::class;


public function getHeaderActions(): array
{
    return [
    ];
}
}