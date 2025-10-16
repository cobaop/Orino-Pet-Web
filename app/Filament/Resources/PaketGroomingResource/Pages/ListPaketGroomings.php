<?php

namespace App\Filament\Resources\PaketGroomingResource\Pages;

use App\Filament\Resources\PaketGroomingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaketGroomings extends ListRecords
{
    protected static string $resource = PaketGroomingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Paket Grooming')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager'])),
        ];
    }

    /**
     * Batasi akses halaman ini hanya untuk role tertentu
     */
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager', 'petshop_employee']), 403);
        parent::mount();
    }
}
