<?php

namespace App\Filament\Resources\LayananKlinikResource\Pages;

use App\Filament\Resources\LayananKlinikResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditLayananKlinik extends EditRecord
{
    protected static string $resource = LayananKlinikResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function canAccessPage(): bool
    {
        return auth()->user()?->hasAnyRole(['docter']);
    }

    protected function authorizeAccess(): void
    {
        abort_unless($this->canAccessPage(), 403);
    }

    protected function canEdit(): bool
    {
        return auth()->user()?->hasAnyRole(['docter']);
    }
    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $updatedRecord = parent::handleRecordUpdate($record, $data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($updatedRecord)
            ->withProperties(['attributes' => $data])
            ->log('Memperbarui layanan klinik: ' . $updatedRecord->nama_layanan);

        return $updatedRecord;
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
