<?php

namespace App\Filament\Resources\DataHewanResource\Pages;

use App\Filament\Resources\DataHewanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditDataHewan extends EditRecord
{
    protected static string $resource = DataHewanResource::class;


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
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record->getKey()]);
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan') // Mengganti "Save changes"
                ->submit('save')
                ->successRedirectUrl($this->getResource()::getUrl('index')), 

            Action::make('cancel')
                ->label('Batal') // Mengganti "Cancel"
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
