<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{

    protected ?string $heading = 'Selamat Datang';

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardWidget::class,
        ];
    }
}
