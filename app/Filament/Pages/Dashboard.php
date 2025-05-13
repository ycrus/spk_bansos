<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // $header = '';
    // if (auth()->user()?->hasRole('Staff Desa')) {
    // }

    protected ?string $heading = null;

    public static function getNavigationLabel(): string
    {
        return 'Beranda';
    }

    public function mount(): void
    {
        $user = auth()->user()?->name;
        if (auth()->user()?->hasRole('Staff Desa')){
            $desa = auth()->user()?->desaStaf?->name;
            $this->heading = "Selamat Datang, {$user} dari {$desa}." ;
        }else{
            $this->heading = "Selamat Datang, {$user}." ;
        }
    }


}
