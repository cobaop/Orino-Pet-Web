<?php

namespace App\Filament\Resources\ReservasiGroomingResource\Pages;

use App\Filament\Resources\ReservasiGroomingResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditReservasiGrooming extends EditRecord
{
    protected static string $resource = ReservasiGroomingResource::class;

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


    protected function canEdit(): bool
    {
        // Hanya super_admin dan manager yang bisa edit
        return auth()->user()?->hasAnyRole(['manager']);
    }
    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $oldAttributes = $record->getOriginal();
        $updatedRecord = parent::handleRecordUpdate($record, $data);

        activity()
        ->causedBy(auth()->user())
        ->performedOn($updatedRecord)
        ->withProperties([
            'attributes' => $data,
            'old' => $oldAttributes,
        ])
        ->log('Memperbarui reservasi grooming: ' . $updatedRecord->id_reservasi_grooming);
        return $updatedRecord;
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load('transaksi');
        return $data;
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan') // Mengganti "Save changes"
                ->submit('save'),

            Action::make('cancel')
                ->label('Batal') // Mengganti "Cancel"
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}

