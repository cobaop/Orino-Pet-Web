<?php

namespace App\Filament\Resources\PenitipanKucingResource\Pages;

use App\Filament\Resources\PenitipanKucingResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPenitipanKucing extends ViewRecord
{
    protected static string $resource = PenitipanKucingResource::class;
    protected static ?string $title = 'Lihat Reservasi Penitipan';

    protected static string $view = 'filament.resources.penitipan-kucing-resource.pages.view-penitipan-kucing';
}
