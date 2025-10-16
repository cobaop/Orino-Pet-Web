<?php

namespace App\Filament\Resources;

class DashboardResource
{
    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\UserChart::class,
            \App\Filament\Widgets\TransaksiHarian::class,
            \App\Filament\Widgets\TransaksiBulanan::class,
            \App\Filament\Widgets\TransaksiTahunan::class,
            \App\Filament\Widgets\JumlahGroomingChart::class,
            \App\Filament\Widgets\JumlahReservasiKlinikChart::class,
        ];
    }
}
