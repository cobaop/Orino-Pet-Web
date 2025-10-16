<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $role = $this->form->getState()['role'] ?? null;

        if ($role) {
            $this->record->assignRole($role);
        }
    }
    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin']), 403);
        parent::mount();
    }
}
