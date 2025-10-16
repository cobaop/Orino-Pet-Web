<?php

namespace App\Filament\Resources\PaketGroomingResource\Pages;

use App\Filament\Resources\PaketGroomingResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreatePaketGrooming extends CreateRecord
{
    protected static string $resource = PaketGroomingResource::class;
    protected static ?string $title = 'Tambah Paket Grooming';

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Paket Grooming Berhasil Ditambahkan')
            ->success()
            ->send();

        $this->redirect($this->getResource()::getUrl('index'));
    }
    protected function canAccessPage(): bool
    {
        // Hanya super_admin dan manager yang bisa mengakses halaman ini
        return auth()->user()?->hasAnyRole(['manager']);
    }

    protected function authorizeAccess(): void
    {
        abort_unless($this->canAccessPage(), 403);
    }

    protected function canCreate(): bool
    {
        // Hanya super_admin dan manager yang bisa membuat data
        return auth()->user()?->hasAnyRole(['manager']);
    }
        protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::handleRecordCreation($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($record)
            ->withProperties(['attributes' => $data])
            ->log('Membuat paket grooming baru: ' . $record->nama_paket);


        return $record;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
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
