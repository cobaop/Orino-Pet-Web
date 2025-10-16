<?php

namespace App\Filament\Resources\ReservasiKlinikResource\Pages;

use App\Filament\Resources\ReservasiKlinikResource;
use Filament\Resources\Pages\ViewRecord;

class ViewReservasiKlinik extends ViewRecord
{
    protected static string $resource = ReservasiKlinikResource::class;

    protected static ?string $title = 'Lihat Reservasi Klinik';
    protected static string $view = 'filament.resources.reservasi-klinik-resource.pages.view-reservasi-klinik';
}

