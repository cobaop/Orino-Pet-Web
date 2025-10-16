<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Resources\Pages\ListRecords;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ViewAction;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin']), 403);
        parent::mount();
    }

    protected function getTableQuery(): Builder
    {
        $rolesToShow = ['docter', 'manager', 'petshop_employee', 'clinic_employee', 'super_admin'];

        return Activity::query()
            ->whereHas('causer.roles', function (Builder $query) use ($rolesToShow) {
                $query->whereIn('name', $rolesToShow);
            })
            ->with(['causer.roles'])
            ->orderBy('created_at', 'desc');
    }

    protected function getTableActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
