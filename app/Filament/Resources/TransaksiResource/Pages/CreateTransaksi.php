<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function canAccessPage(): bool
    {
        // Hanya super_admin dan manager yang bisa mengakses halaman ini
        // return auth()->user()?->hasAnyRole(['super_admin', 'manager']);
        return false; // create tidak dapat dilakukan
    }

    protected function authorizeAccess(): void
    {
        abort_unless($this->canAccessPage(), 403);
    }

    protected function canCreate(): bool
    {
        // // Hanya super_admin dan manager yang bisa membuat data
        // return auth()->user()?->hasAnyRole(['super_admin', 'manager']);
        return false; // create tidak dapat dilakukan
    }
        protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::handleRecordCreation($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($record)
            ->withProperties(['attributes' => $data])
            ->log('Membuat transaksi baru: ' . $record->id_transaksi);

        return $record;
    }
    public function mount(): void
    {
        abort(403, 'Halaman pembuatan transaksi tidak diizinkan.');
    }
}
