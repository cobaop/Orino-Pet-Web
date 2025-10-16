<?php

namespace App\Filament\Resources\KandangResource\Pages;

use App\Filament\Resources\KandangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditKandang extends EditRecord
{
    protected static string $resource = KandangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function canAccessPage(): bool
    {
        return auth()->user()?->hasAnyRole(['manager']);
    }

    protected function authorizeAccess(): void
    {
        abort_unless($this->canAccessPage(), 403);
    }

    protected function canEdit(): bool
    {
        return auth()->user()?->hasAnyRole(['manager']);
    }
    protected function handleRecordUpdate($record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Ambil data sebelum di-update
        $oldAttributes = $record->getOriginal();

        $updatedRecord = parent::handleRecordUpdate($record, $data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($updatedRecord)
            ->withProperties([
                'attributes' => $data,
                'old' => $oldAttributes,
            ])
            ->log('Memperbarui kandang: ' . $updatedRecord->kode_kandang);

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
