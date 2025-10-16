<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Mencegah penyimpanan field 'role' ke tabel users.
     * Juga memblokir perubahan apapun jika user adalah owner.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->hasRole('owner')) {
            return []; // Jangan ubah apapun untuk user owner
        }

        unset($data['role']); // 'role' tidak ada di tabel users
        return $data;
    }

    /**
     * Setelah data disimpan, sinkronisasi role jika bukan owner.
     */
    protected function afterSave(): void 
    {
        $role = $this->form->getState()['role'] ?? null;

        // Ambil record fresh dari database sebelum perubahan role
        $freshRecordBeforeSync = User::find($this->record->id);
        $oldRoles = $freshRecordBeforeSync->getRoleNames()->toArray();

        if (in_array($role, ['super_admin', 'owner'])) {
            // Proteksi tambahan di backend
            return;
        }

        if ($role) {
            $this->record->syncRoles([$role]);
        }

        // Ambil lagi role setelah di-sync
        $newRoles = $this->record->fresh()->getRoleNames()->toArray();

        // Gabungkan role ke dalam attributes & old agar tampil di viewer activitylog
        $currentAttributes = $this->record->fresh()->toArray();
        $originalAttributes = $this->record->getOriginal();

        $currentAttributes['role'] = implode(', ', $newRoles);
        $originalAttributes['role'] = implode(', ', $oldRoles);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($this->record)
            ->withProperties([
                'attributes' => $currentAttributes,
                'old' => $originalAttributes,
            ])
            ->log('Memperbarui User: ' . $this->record->id);
    }

    /**
     * Mencegah akses ke halaman edit jika user yang diedit adalah owner.
     */
    public function mount($record): void
    {
        parent::mount($record);

        // Cegah akses edit untuk user dengan role super_admin atau owner
        if ($this->record->hasRole('super_admin') || $this->record->hasRole('owner')) {
            abort(403, 'Forbidden: Cannot edit this user.');
        }
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

     protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan') // Ganti label tombol save
                ->submit('save')            // Submit form
                ->successRedirectUrl($this->getResource()::getUrl('index')),

                
            Action::make('cancel')
                ->label('Batal') // Ganti label tombol cancel
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
