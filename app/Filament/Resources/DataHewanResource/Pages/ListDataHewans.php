<?php

namespace App\Filament\Resources\DataHewanResource\Pages;

use App\Filament\Resources\DataHewanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataHewans extends ListRecords
{
    protected static string $resource = DataHewanResource::class;

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['docter', 'clinic_employee']), 403);
        parent::mount();
    }
}
