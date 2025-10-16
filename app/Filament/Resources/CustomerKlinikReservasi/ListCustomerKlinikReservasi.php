<?php

namespace App\Filament\Resources\CustomerKlinikReservasiResource\Pages;

use App\Filament\Resources\CustomerKlinikReservasiResource;
use Filament\Resources\Pages\ListRecords;

class ListCustomerKlinikReservasi extends ListRecords
{
    protected static string $resource = CustomerKlinikReservasiResource::class;

        public function mount(): void
        {
            abort_unless(auth()->user()?->hasAnyRole(['docter', 'clinic_employee']), 403);
            parent::mount();
        }
}
