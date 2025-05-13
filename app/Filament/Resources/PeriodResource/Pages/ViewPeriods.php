<?php

namespace App\Filament\Resources\PeriodResource\Pages;

use App\Filament\Resources\PeriodResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPeriods extends ViewRecord
{
    protected static string $resource = PeriodResource::class;


public function getHeaderActions(): array
{
    return [
    ];
}
}