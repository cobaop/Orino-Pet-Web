<?php

namespace App\Filament\Resources\PenitipanKucingResource\Pages;

use App\Filament\Resources\PenitipanKucingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenitipanKucings extends ListRecords
{
    protected static string $resource = PenitipanKucingResource::class;

        /**
     * Batasi akses halaman ini hanya untuk role tertentu
     */

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager', 'petshop_employee']), 403);
        parent::mount();

        \App\Models\PenitipanKucing::where('is_read', false)
            ->whereHas('transaksi', fn ($q) => $q->where('status_pembayaran', 'lunas'))
            ->update(['is_read' => true]);
    }
}
