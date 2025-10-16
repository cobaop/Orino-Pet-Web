<?php

namespace App\Filament\Resources\ReservasiKlinikResource\Pages;

use App\Filament\Resources\ReservasiKlinikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReservasiKliniks extends ListRecords
{
    protected static string $resource = ReservasiKlinikResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make()
    //             ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'docter'])),
    //     ];
    // }

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['docter', 'clinic_employee']), 403);
        parent::mount();
    }
}

