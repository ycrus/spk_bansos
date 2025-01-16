<?php

namespace App\Filament\Widgets;

use App\Models\Period;
use App\Models\Program;
use App\Models\Receiver;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Penerima', Receiver::count())
                ->description('Tota penerima yang terdaftar')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->chart([1, 5, 3, 4, 8, 10])
                ->color('success'),
            Stat::make('Total Program', Program::count())
                ->description('Tota Program yang ada')
                ->descriptionIcon('heroicon-c-cube', IconPosition::Before)
                ->chart([1, 5, 3, 4, 8, 10])
                ->color('success'),
            Stat::make('Total Period', Period::count())
                ->description('Tota Period yang ada')
                ->descriptionIcon('heroicon-o-squares-2x2', IconPosition::Before)
                ->chart([1, 5, 3, 4, 8, 10])
                ->color('success')
        ];
    }
}
