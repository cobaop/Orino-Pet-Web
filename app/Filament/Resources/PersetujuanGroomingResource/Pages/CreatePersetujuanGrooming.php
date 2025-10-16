<?php

namespace App\Filament\Resources\PersetujuanGroomingResource\Pages;

use App\Filament\Resources\PersetujuanGroomingResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;


class CreatePersetujuanGrooming extends CreateRecord
{
    protected static string $resource = PersetujuanGroomingResource::class;
        protected function canAccessPage(): bool
    {
        return auth()->user()?->hasAnyRole(['manager']);
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
            ->log('Membuat modal persetujuan baru: ' . $record->deskripsi);

        return $record;
    }
        protected function authorizeAccess(): void
    {
        abort_unless($this->canAccessPage(), 403);
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
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

