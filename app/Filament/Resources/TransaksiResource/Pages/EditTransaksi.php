<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksi extends EditRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function canAccessPage(): bool
    {
        // Hanya super_admin dan manager yang bisa mengakses halaman edit
        return auth()->user()?->hasAnyRole(['manager']);
    }

    protected function authorizeAccess(): void
    {
        abort_unless($this->canAccessPage(), 403);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Tombol delete hanya muncul untuk super_admin
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('manager')),
        ];
    }

    protected function canEdit(): bool
    {
        // Hanya super_admin dan manager yang bisa edit
        return auth()->user()?->hasAnyRole(['manager']);
    }
    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $updatedRecord = parent::handleRecordUpdate($record, $data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($updatedRecord)
            ->withProperties(['attributes' => $data])
            ->log('Memperbarui transaksi: ' . $updatedRecord->id_transaksi);

        return $updatedRecord;
    }
    public function mount($record): void
    {
        abort(403, 'Halaman edit tidak diizinkan.');
    }

}
