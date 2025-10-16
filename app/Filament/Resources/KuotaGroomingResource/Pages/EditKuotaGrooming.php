<?php

namespace App\Filament\Resources\KuotaGroomingResource\Pages;

use App\Filament\Resources\KuotaGroomingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class EditKuotaGrooming extends EditRecord
{
    protected static string $resource = KuotaGroomingResource::class;

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

        if ($this->record && Carbon::parse($this->record->tanggal_ketersediaan)->isPast()) {
            Notification::make()
                ->title('Tidak Bisa Edit')
                ->body('Kuota dengan tanggal yang sudah lewat tidak dapat diedit.')
                ->danger()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        }
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make()
    //             ->visible(fn () => auth()->user()?->hasRole('super_admin'))
    //             ->before(function ($record) {
    //                 $oldAttributes = $record->getOriginal();

    //                 activity()
    //                     ->causedBy(auth()->user())
    //                     ->performedOn($record)
    //                     ->withProperties(['old' => $oldAttributes])
    //                     ->log('Menghapus kuota grooming dari halaman edit: ' . $record->tanggal_ketersediaan);
    //             }),
    //     ];
    // }

    protected function canEdit(): bool
    {
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
            ->log('Memperbarui kuota grooming: ' . $updatedRecord->tanggal_ketersediaan);

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
