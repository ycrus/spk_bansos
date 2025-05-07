<?php

namespace App\Filament\Widgets;

use App\Models\Period;
use App\Models\Program;
use App\Models\Receiver;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Penerima', Receiver::count())
                ->description('Total penerima yang terdaftar')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->chart([1, 5, 3, 4, 8, 10])
                ->color('success'),
            Stat::make('Total Program', Program::count())
                ->description('Total Program yang ada')
                ->descriptionIcon('heroicon-c-cube', IconPosition::Before)
                ->chart([1, 5, 3, 4, 8, 10])
                ->color('success'),
            Stat::make('Total Period', Period::count())
                ->description('Total Period yang ada')
                ->descriptionIcon('heroicon-o-squares-2x2', IconPosition::Before)
                ->chart([1, 5, 3, 4, 8, 10])
                ->color('success')
        ];
    }

    protected function shouldShow(): bool
    {
        return auth()->user()?->hasRole(['Super Admin']);
    }


}
