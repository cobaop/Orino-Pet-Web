<?php

namespace App\Filament\Resources\LayananKlinikResource\Pages;

use App\Filament\Resources\LayananKlinikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayananKliniks extends ListRecords
{
    protected static string $resource = LayananKlinikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol "Create" hanya untuk super_admin dan docter
            Actions\CreateAction::make()
                    ->label('Tambah Layanan Klinik')
                ->visible(fn () => auth()->user()?->hasAnyRole(['docter'])),
        ];
    }

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['docter', 'clinic_employee']), 403);
        parent::mount();
    }
}
