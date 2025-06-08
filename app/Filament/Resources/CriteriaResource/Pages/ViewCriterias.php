<?php

namespace App\Filament\Resources\CriteriaResource\Pages;

use App\Filament\Resources\CriteriaResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCriterias extends ViewRecord
{
    protected static string $resource = CriteriaResource::class;


public function getHeaderActions(): array
{
    return [
    ];
}
}