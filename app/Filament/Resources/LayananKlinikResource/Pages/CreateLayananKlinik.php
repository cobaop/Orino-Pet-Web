<?php

namespace App\Filament\Resources\LayananKlinikResource\Pages;

use App\Filament\Resources\LayananKlinikResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;

class CreateLayananKlinik extends CreateRecord
{
    protected static string $resource = LayananKlinikResource::class;
    protected static ?string $title = 'Tambah Layanan Klinik';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function canAccessPage(): bool
    {
        // Hanya super_admin dan docter yang boleh mengakses halaman create
        return auth()->user()?->hasAnyRole(['docter']);
    }

    protected function authorizeAccess(): void
    {
        abort_unless($this->canAccessPage(), 403);
    }

    protected function canCreate(): bool
    {
        // Hanya super_admin dan docter yang bisa buat data
        return auth()->user()?->hasAnyRole(['docter']);
    }
        protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::handleRecordCreation($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($record)
            ->withProperties(['attributes' => $data])
            ->log('Membuat layanan klinik baru: ' . $record->nama_layanan);

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
