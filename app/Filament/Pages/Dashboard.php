<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardWidget::class,
        ];
    }

    protected function shouldShow(): bool
    {
        return auth()->user()?->hasRole(['Super Admin']);
    }
}
