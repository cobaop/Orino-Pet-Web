<?php

namespace App\Filament\Resources\PersetujuanPenitipanResource\Pages;

use App\Filament\Resources\PersetujuanPenitipanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditPersetujuanPenitipan extends EditRecord
{
    protected static string $resource = PersetujuanPenitipanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
