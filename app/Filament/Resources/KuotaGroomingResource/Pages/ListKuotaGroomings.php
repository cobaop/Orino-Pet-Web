<?php

namespace App\Filament\Resources\KuotaGroomingResource\Pages;

use App\Filament\Resources\KuotaGroomingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKuotaGroomings extends ListRecords
{
    protected static string $resource = KuotaGroomingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kuota Grooming')
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
