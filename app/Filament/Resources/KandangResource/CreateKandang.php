<?php

namespace App\Filament\Resources\KandangResource\Pages;

use App\Filament\Resources\KandangResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateKandang extends CreateRecord
{
    protected static string $resource = KandangResource::class;

    protected static ?string $title = 'Tambah Kandang';

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

    protected function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['manager']);
    }
        protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::handleRecordCreation($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($record)
            ->withProperties(['attributes' => $data])
            ->log('Membuat kandang baru: ' . $record->kode_kandang);

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
