<?php

namespace App\Filament\Resources\ReservasiKlinikResource\Pages;

use App\Filament\Resources\ReservasiKlinikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditReservasiKlinik extends EditRecord
{
    protected static string $resource = ReservasiKlinikResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        if (auth()->user()?->hasRole('docter')) {
            return [
                Actions\DeleteAction::make(),
            ];
        }

        return []; // docter bisa edit tapi tidak hapus
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
        $oldAttributes = $record->getOriginal();
        $updatedRecord = parent::handleRecordUpdate($record, $data);

        activity()
        ->causedBy(auth()->user())
        ->performedOn($updatedRecord)
        ->withProperties([
            'attributes' => $data,
            'old' => $oldAttributes,
        ])
        ->log('Memperbarui reservasi klinik: ' . $updatedRecord->id_reservasi_klinik);

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

