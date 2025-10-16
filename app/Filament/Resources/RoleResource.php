<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Model;

class RoleResource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'docter', 'clinic_employee', 'manager', 'petshop_employee']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'docter', 'manager']);
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'docter', 'manager']);
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
