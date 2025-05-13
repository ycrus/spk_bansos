<?php

namespace App\Filament\Resources\ParameterResource\Pages;

use App\Filament\Resources\ParameterResource;
use Filament\Resources\Pages\ViewRecord;

class ViewParameters extends ViewRecord
{
    protected static string $resource = ParameterResource::class;


public function getHeaderActions(): array
{
    return [
    ];
}
}