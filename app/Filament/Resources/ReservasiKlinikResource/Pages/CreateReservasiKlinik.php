<?php

namespace App\Filament\Resources\ReservasiKlinikResource\Pages;

use App\Filament\Resources\ReservasiKlinikResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateReservasiKlinik extends CreateRecord
{
    protected static string $resource = ReservasiKlinikResource::class;

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

    protected function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['docter']);
    }
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::handleRecordCreation($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($record)
            ->withProperties(['attributes' => $data])
            ->log('Membuat reservasi klinik baru: ' . $record->id_reservasi_klinik);

        return $record;
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Simpan') // Ganti "Create" jadi "Simpan"
                ->submit('create'),

            Action::make('create_another')
                ->label('Simpan & tambah lagi') // Ganti "Create & create another"
                ->submit('createAnother'),

            Action::make('cancel')
                ->label('Batal')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
