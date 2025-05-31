<?php

namespace App\Filament\Widgets;

use App\Models\Receiver;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = [];
    
        if (auth()->user()?->hasRole('Staff Desa')) {
            $desaId = auth()->user()?->desa;
            $desa = auth()->user()?->desaStaf?->name;
            $stats[] = Stat::make('Total Data Alternatif', Receiver::where('kelurahan', $desaId)->count())
                        ->description('Total penerima yang terdaftar')
                        ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                        ->chart([1, 5, 3, 4, 8, 10])
                        ->color('success');
                        
            $stats[] =  Stat::make('Need Approval', Receiver::where('status', 'Need Approval')
                        ->where('kelurahan', $desaId)->count())
                        ->description("Jumlah penerima yang menunggu persetujuan {$desa}")
                        ->descriptionIcon('heroicon-m-clock', \Filament\Support\Enums\IconPosition::Before)
                        ->chart([2, 4, 3, 5, 6, 7])
                        ->color('warning');

            $stats[] =  Stat::make('Rejected', Receiver::where('status', 'Rejected')
                        ->where('kelurahan', $desaId)->count())
                        ->description('Jumlah penerima rejected')
                        ->descriptionIcon('heroicon-m-minus-circle', \Filament\Support\Enums\IconPosition::Before)
                        ->chart([2, 4, 3, 5, 6, 7])
                        ->color('danger');
        }else{
            $stats[] = Stat::make('Total Data Alternatif', Receiver::where('status', '!=', 'Draft')->count())
                        ->description('Total penerima yang terdaftar')
                        ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                        ->chart([1, 5, 3, 4, 8, 10])
                        ->color('success');

            $stats[] =  Stat::make('Need Approval', Receiver::where('status', 'Need Approval')->count())
                        ->description('Jumlah penerima yang menunggu persetujuan')
                        ->descriptionIcon('heroicon-m-clock', \Filament\Support\Enums\IconPosition::Before)
                        ->chart([2, 4, 3, 5, 6, 7])
                        ->color('warning');

            $stats[] =  Stat::make('Rejected', Receiver::where('status', 'Rejected')->count())
                        ->description('Jumlah penerima rejected')
                        ->descriptionIcon('heroicon-m-minus-circle', \Filament\Support\Enums\IconPosition::Before)
                        ->chart([2, 4, 3, 5, 6, 7])
                        ->color('danger');
        }
        return $stats;
    }
}
