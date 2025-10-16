<?php

namespace App\Filament\Resources\PaketGroomingResource\Pages;

use App\Filament\Resources\PaketGroomingResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaketGrooming extends EditRecord
{
    protected static string $resource = PaketGroomingResource::class;

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Paket Grooming Berhasil Diperbarui')
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('index'));
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
            'old' => $oldAttributes,
            'attributes' => $data
        ])
        ->log('Memperbarui paket grooming: ' . $updatedRecord->nama_paket);

        return $updatedRecord;
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan'), // Ganti label tombol save

            Action::make('cancel')
                ->label('Batal') // Ganti label tombol cancel
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}

