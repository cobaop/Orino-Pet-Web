<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;


    public function mount(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['manager', 'petshop_employee']), 403);
        parent::mount();
    }
}
