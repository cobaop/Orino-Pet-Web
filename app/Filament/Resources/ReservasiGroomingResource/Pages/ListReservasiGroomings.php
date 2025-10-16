<?php

namespace App\Filament\Resources\ReservasiGroomingResource\Pages;

use App\Filament\Resources\ReservasiGroomingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\ReservasiGrooming;

class ListReservasiGroomings extends ListRecords
{
    protected static string $resource = ReservasiGroomingResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make()
    //             ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'manager'])),
    //     ];
    // }

        /**
     * Batasi akses halaman ini hanya untuk role tertentu
     */
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager', 'petshop_employee']), 403);
        parent::mount();

        ReservasiGrooming::where('is_read', false)
            ->whereHas('transaksi', fn ($q) => $q->where('status_pembayaran', 'lunas'))
            ->update(['is_read' => true]);
    }
}
