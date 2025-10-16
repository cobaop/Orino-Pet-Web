<?php

namespace App\Filament\Resources\KandangResource\Pages;

use App\Filament\Resources\KandangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKandangs extends ListRecords
{
    protected static string $resource = KandangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kandang')
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager'])),
        ];
    }

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager', 'petshop_employee']), 403);
        parent::mount();
    }
}
