<?php

namespace App\Filament\Resources\PersetujuanPenitipanResource\Pages;

use App\Filament\Resources\PersetujuanPenitipanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersetujuanPenitipans extends ListRecords
{
    protected static string $resource = PersetujuanPenitipanResource::class;

       protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Modal')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager'])),
        ];
    }
        public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager', 'petshop_employee']), 403);
        parent::mount();

    }
}
