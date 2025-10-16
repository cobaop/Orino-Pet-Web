<?php

namespace App\Filament\Resources\PersetujuanGroomingResource\Pages;

use App\Filament\Resources\PersetujuanGroomingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersetujuanGroomings extends ListRecords
{
    protected static string $resource = PersetujuanGroomingResource::class;

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
