<?php

namespace App\Filament\Resources\ReservasiGroomingResource\Pages;

use App\Filament\Resources\ReservasiGroomingResource;
use Filament\Resources\Pages\ViewRecord;

class ViewReservasiGrooming extends ViewRecord
{
    protected static string $resource = ReservasiGroomingResource::class;

    protected static ?string $title = 'Lihat Reservasi Grooming';

    protected static string $view = 'filament.resources.reservasi-grooming-resource.pages.view-reservasi-grooming';
}
