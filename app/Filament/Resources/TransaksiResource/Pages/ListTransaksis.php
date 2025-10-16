<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksis extends ListRecords
{
    protected static string $resource = TransaksiResource::class;
    // ini button create, saya non aktifkan
    // protected function getHeaderActions(): array
    // {
    //     // return [
    //     //     Actions\CreateAction::make()
    //     //         ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'owner'])),
    //     // ];
    // }

    /**
     * Batasi akses halaman ini hanya untuk role tertentu
     */
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager']), 403);
        parent::mount();
    }
}
